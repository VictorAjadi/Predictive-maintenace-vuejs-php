<template>
    <div class="header">
        <h1>Precious Project</h1>
        <div class="nav">
            <router-link to="/sign-in">Sign in</router-link>
            <router-link to="/sign-up" class="sign-up">Sign up</router-link>
        </div>
    </div>
    <form @submit.prevent="handleSubmit">
        <div class="container">
            <h2>Sign Up</h2>
            <input type="text" placeholder="username" v-model="username" required>
            <input type="email" placeholder="Email" v-model="email" required>
            <input type="password" placeholder="Password" v-model="password" required>
            <button type="submit">Sign In</button>
            <p>Already a member <router-link to="/sign-in">sign in?</router-link></p>
        </div>
    </form>
  </template>
  
  <script>
  import axios from 'axios';
  import { toast } from 'vue3-toastify'; // Import the toast function

  export default {
    name: 'SignIn',
    data() {
      return {
        username: '',
        email: '',
        password: '',
      };
    },
    methods: {
        async handleSubmit() {
        // Handle form submission here (e.g., send data to a backend server)
        try {
            const response = await axios.post('http://localhost:8000/api/adduser.php', {
            email: this.email,
            password: this.password,
            username: this.username // Fixed variable name to 'username'
            },{
            headers: {
                'Content-Type': 'application/json'
            },
            withCredentials: true // Include credentials in the request
            });
            
            // Check if the response indicates success
            if (response.data.status === 'success') {
            toast.success('Sign up was successful!');

            // Create a session cookie on the frontend
            document.cookie = `session=${response.data.session_id}; path=/; max-age=3600`;

            // Redirect to the 'temperature' route
            window.location.href = '/temperature';
            } else {
            toast.error('Sign up failed: ' + response.data.message);
            console.log(response.data.message)
            }
        } catch (error) {
            toast.error('An error occurred while signing up, please try again later');
            console.log(error)
        }
        },
    },
  };
  </script>
  
  <style scoped>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0c3fc, #8ec5fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .header {
            width: 100%;
            background-color: white;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
        }
        .header h1 {
            margin-left: 20px;
            color: #00aaff;
        }
        .header .nav {
            margin-right: 20px;
        }
        .header .nav a {
            margin-left: 10px;
            text-decoration: none;
            color: #00aaff;
            padding: 8px 16px;
            border: 1px solid #00aaff;
            border-radius: 4px;
        }
        .header .nav a.sign-up {
            background-color: #00aaff;
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-top: 100px;
            width: 90%;
            max-width: 400px;
        }
        .container h2 {
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 2rem;
            color: #333;
        }
        .container input[type="text"],
        .container input[type="email"],
        .container input[type="password"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .container button {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #555;
        }
        .container p {
            margin-top: 20px;
        }
        .container p a {
            color: #00aaff;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .header h1 {
                font-size: 18px;
            }
            .container {
                padding: 20px;
            }
        }
    </style>
  