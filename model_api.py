import os  # Add this line
from flask import Flask, request, jsonify, send_from_directory
from flask import Flask, request, jsonify, render_template
import numpy as np
import torch
import time
from torchvision import transforms
from PIL import Image
import torch.nn as nn  
import torch.nn.functional as F
from scipy.ndimage import binary_opening, binary_closing
from torchvision import transforms

# Define the model architecture (ensure it matches your trained model)
class ConvBlock(nn.Module):
    def __init__(self, in_channels, out_channels):
        super().__init__()
        self.conv1 = nn.Conv2d(in_channels, out_channels, 3, padding=1)
        self.bn1 = nn.BatchNorm2d(out_channels)
        self.conv2 = nn.Conv2d(out_channels, out_channels, 3, padding=1)
        self.bn2 = nn.BatchNorm2d(out_channels)

    def forward(self, x):
        x = F.relu(self.bn1(self.conv1(x)))
        x = F.relu(self.bn2(self.conv2(x)))
        return x

class UNet(nn.Module):
    def __init__(self):
        super().__init__()
        self.encoder1 = ConvBlock(3, 64)
        self.encoder2 = ConvBlock(64, 128)
        self.encoder3 = ConvBlock(128, 256)
        self.encoder4 = ConvBlock(256, 512)
        
        self.pool = nn.MaxPool2d(2)
        self.bottleneck = ConvBlock(512, 1024)
        
        self.upconv4 = nn.ConvTranspose2d(1024, 512, 2, stride=2)
        self.decoder4 = ConvBlock(1024, 512)
        
        self.upconv3 = nn.ConvTranspose2d(512, 256, 2, stride=2)
        self.decoder3 = ConvBlock(512, 256)
        
        self.upconv2 = nn.ConvTranspose2d(256, 128, 2, stride=2)
        self.decoder2 = ConvBlock(256, 128)
        
        self.upconv1 = nn.ConvTranspose2d(128, 64, 2, stride=2)
        self.decoder1 = ConvBlock(128, 64)
        
        self.final_conv = nn.Conv2d(64, 1, 1)

    def forward(self, x):
        e1 = self.encoder1(x)
        e2 = self.encoder2(self.pool(e1))
        e3 = self.encoder3(self.pool(e2))
        e4 = self.encoder4(self.pool(e3))
        
        b = self.bottleneck(self.pool(e4))
        
        d4 = self.decoder4(torch.cat([e4, self.upconv4(b)], dim=1))
        d3 = self.decoder3(torch.cat([e3, self.upconv3(d4)], dim=1))
        d2 = self.decoder2(torch.cat([e2, self.upconv2(d3)], dim=1))
        d1 = self.decoder1(torch.cat([e1, self.upconv1(d2)], dim=1))
        
        return torch.sigmoid(self.final_conv(d1))

# Initialize Flask app
app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = 'static'  # Ensure the 'static' folder exists

# Instantiate the model
model = UNet()

# Load the state_dict
# Use `torch.load` with `weights_only=True`
model.load_state_dict(torch.load('unet_model.pth', map_location=torch.device('cpu'), weights_only=True))


# Set the model to evaluation mode
model.eval()

# Define any transformations for input preprocessing
preprocess = transforms.Compose([
    transforms.Resize((256, 256)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
])

@app.route('/')
def index():
    return render_template('index.html')

import numpy as np
from PIL import ImageDraw


@app.route('/predict', methods=['POST'])
def predict():
    if 'file' not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    # Load and preprocess image
    file = request.files['file']
    image = Image.open(file).convert("RGB")
    input_tensor = preprocess(image).unsqueeze(0)  # Add batch dimension

    # Generate unique filename IDs
    unique_id = int(time.time())
    uploaded_image_path = os.path.join(app.config['UPLOAD_FOLDER'], f'uploaded_image_{unique_id}.png')
    mask_path = os.path.join(app.config['UPLOAD_FOLDER'], f'prediction_mask_{unique_id}.png')
    overlay_path = os.path.join(app.config['UPLOAD_FOLDER'], f'overlay_image_{unique_id}.png')

    # Save the uploaded image
    image.save(uploaded_image_path)

    # Perform prediction
    with torch.no_grad():
        output = model(input_tensor)

    # Convert model output to binary mask
    output_mask = output.squeeze().cpu().numpy()
    binary_mask = (output_mask > 0.001).astype(np.uint8)  # Threshold to binary (0 or 1)

    # Morphological operations to clean up the mask
    binary_mask = binary_opening(binary_mask, structure=np.ones((2, 2))).astype(np.uint8)
    binary_mask = binary_closing(binary_mask, structure=np.ones((2, 2))).astype(np.uint8)

    # Save the mask image
    mask_image = Image.fromarray(binary_mask * 255)
    mask_image.save(mask_path)

    # Resize original image to match mask dimensions for overlay
    image = image.resize(mask_image.size)
    overlay = Image.blend(image, mask_image.convert("RGB"), alpha=0.5)
    overlay.save(overlay_path)

    # Return paths to the uploaded, mask, and overlay images in JSON response
    return jsonify({
        "uploaded_image": f"/{uploaded_image_path}",
        "prediction": f"/{mask_path}",
        "overlay": f"/{overlay_path}"
    })


if __name__ == '__main__':
    app.run(debug=True)
