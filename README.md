Here's a detailed README file for your Docker-based web application project, which includes setting up, building, and deploying two versions of a web app:

---

# Docker Web App Project

## Project Overview

This project involves creating, building, and deploying two versions of a web application using Docker containers. The first version is a simple static web page hosted on Docker Hub, and the second version is a more dynamic web application deployed on AWS ECS Fargate.

---

## Table of Contents

- [Project Structure](#project-structure)
- [Version 1: Static Webpage](#version-1-static-webpage)
  - [Prerequisites](#prerequisites)
  - [Setup Instructions](#setup-instructions)
  - [Build and Run](#build-and-run)
  - [Push to Docker Hub](#push-to-docker-hub)
- [Version 2: Dynamic Webpage](#version-2-dynamic-webpage)
  - [Prerequisites](#prerequisites-1)
  - [Setup Instructions](#setup-instructions-1)
  - [Build and Run](#build-and-run-1)
  - [Push to AWS ECR](#push-to-aws-ecr)
  - [Deploy on AWS ECS Fargate](#deploy-on-aws-ecs-fargate)
- [Useful Commands](#useful-commands)

---

## Project Structure

```
docker-web-app/
│
├── version1/
│   ├── Dockerfile
│   └── index.html
│
└── version2/
    ├── Dockerfile
    ├── index.html
    └── process.php
```

---

## Version 1: Static Webpage

### Prerequisites

- Docker installed on your system.
- A [Docker Hub](https://hub.docker.com/) account.
- Git version control (recommended).

### Setup Instructions

1. **Create the Project Directory:**

    ```sh
    mkdir -p docker-web-app/version1
    cd docker-web-app/version1
    ```

2. **Create `index.html`:**

    ```html
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
    </head>
    <body>
        <h2>Login Form</h2>
        <form action="/login" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>
    ```

3. **Create `Dockerfile`:**

    ```Dockerfile
    # Use an official Nginx image as a parent image
    FROM nginx:alpine

    # Copy the HTML file to the Nginx image
    COPY index.html /usr/share/nginx/html
    ```

### Build and Run

1. **Build the Docker Image:**

    ```sh
    docker build -t docker-web-app:v1 .
    ```

2. **Run the Docker Container:**

    ```sh
    docker run -d -p 900:80 docker-web-app:v1
    ```

3. **Test the Application:**

    Open your browser and navigate to `http://localhost:900` to see the login page.

### Push to Docker Hub

1. **Tag the Image:**

    ```sh
    docker tag docker-web-app:v1 <your-dockerhub-username>/docker-web-app:v1
    ```

2. **Push the Image:**

    ```sh
    docker push <your-dockerhub-username>/docker-web-app:v1
    ```

---

## Version 2: Dynamic Webpage

### Prerequisites

- Docker installed on your system.
- [AWS CLI](https://aws.amazon.com/cli/) installed and configured.
- An [AWS account](https://aws.amazon.com/).
- An IAM user with appropriate permissions for ECR and ECS.
- Git version control (recommended).

### Setup Instructions

1. **Create the Project Directory:**

    ```sh
    mkdir -p ../version2
    cd ../version2
    ```

2. **Create `index.html`:**

    ```html
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
    </head>
    <body>
        <h2>Login Form</h2>
        <form action="process.php" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>
    ```

3. **Create `process.php`:**

    ```php
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = htmlspecialchars($_POST["username"]);
        $password = htmlspecialchars($_POST["password"]);
        echo "Username: " . $username . "<br>";
        echo "Password: " . $password;
    }
    ?>
    ```

4. **Create `Dockerfile`:**

    ```Dockerfile
    # Use the official PHP image with Apache
    FROM php:7.4-apache

    # Copy application files to the Apache document root
    COPY . /var/www/html/
    ```

### Build and Run

1. **Build the Docker Image:**

    ```sh
    docker build -t docker-web-app:v2 .
    ```

2. **Run the Docker Container:**

    ```sh
    docker run -d -p 9000:80 docker-web-app:v2
    ```

3. **Test the Application:**

    Open your browser and navigate to `http://localhost:9000` to see the login page. Submit the form to test the PHP processing.

### Push to AWS ECR

1. **Create an ECR Repository:**

    ```sh
    aws ecr create-repository --repository-name docker-web-app
    ```

2. **Authenticate Docker to the ECR Registry:**

    ```sh
    aws ecr get-login-password --region <your-region> | docker login --username AWS --password-stdin <aws_account_id>.dkr.ecr.<your-region>.amazonaws.com
    ```

3. **Tag the Image:**

    ```sh
    docker tag docker-web-app:v2 <aws_account_id>.dkr.ecr.<your-region>.amazonaws.com/docker-web-app:v2
    ```

4. **Push the Image:**

    ```sh
    docker push <aws_account_id>.dkr.ecr.<your-region>.amazonaws.com/docker-web-app:v2
    ```

### Deploy on AWS ECS Fargate

1. **Create a Task Definition:**

    ```sh
    aws ecs register-task-definition --cli-input-json file://task-definition.json
    ```

    *Example `task-definition.json`:*
    ```json
    {
        "family": "docker-web-app",
        "networkMode": "awsvpc",
        "requiresCompatibilities": ["FARGATE"],
        "cpu": "256",
        "memory": "512",
        "containerDefinitions": [
            {
                "name": "docker-web-app",
                "image": "<aws_account_id>.dkr.ecr.<your-region>.amazonaws.com/docker-web-app:v2",
                "portMappings": [
                    {
                        "containerPort": 80,
                        "hostPort": 80
                    }
                ]
            }
        ]
    }
    ```

2. **Create an ECS Cluster:**

    ```sh
    aws ecs create-cluster --cluster-name docker-web-app-cluster
    ```

3. **Run a Fargate Task:**

    ```sh
    aws ecs run-task --cluster docker-web-app-cluster --launch-type FARGATE --task-definition docker-web-app
    ```

4. **Access the Web App:**

    Find the public IP of the running Fargate task and navigate to it in your browser to access your web app.

---

## Useful Commands

- **List Docker Images:**

    ```sh
    docker images
    ```

- **Stop Docker Containers:**

    ```sh
    docker ps -q | xargs docker stop
    ```

- **Remove Docker Containers:**

    ```sh
    docker ps -a -q | xargs docker rm
    ```

- **Remove Docker Images:**

    ```sh
    docker images -q | xargs docker rmi
    ```

- **AWS CLI Configure:**

    ```sh
    aws configure
    ```

---

Feel free to customize the README file according to your project's specific requirements and preferences.

---

