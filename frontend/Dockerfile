FROM node:18-alpine

WORKDIR /app

# Copy package files
COPY package*.json ./


# Install dependencies
RUN npm install

# Copy project files
COPY . .

# Build the app
RUN npm run build

# Expose port 5173 for Vite
EXPOSE 5173

# Start the app
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
