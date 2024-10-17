<template>
    <div class="full-container d-flex gap-3 jusitify-content-between align-items-start position-relative">
      <div class="sidebar" :class="['sidebar', { collapsed: isCollapsed }]">
        <div class="sidebar-header">
          <h4 v-if="!isCollapsed">PETER PRECIOUS</h4>
          <button class="collapse-btn" @click="toggleCollapse">
            <span v-if="isCollapsed">&#9654;</span>
            <span v-else>&#9664;</span>
          </button>
        </div>
        <ul class="nav-links">
          <li>
            <router-link to="/temperature">
              <i class="fas fa-thermometer-half"></i> <span v-if="!isCollapsed">Temperature</span>
            </router-link>
          </li>
          <li>
            <router-link to="/humidity">
              <i class="fas fa-water"></i> <span v-if="!isCollapsed">Humidity</span>
            </router-link>
          </li>
          <li>
            <router-link to="/weight">
              <i class="fas fa-weight-hanging"></i> <span v-if="!isCollapsed">Weight</span>
            </router-link>
          </li>
        </ul>
        <div class="logout">
        <button @click="logout">
          <i class="fas fa-sign-out-alt"></i>
          <span v-if="!isCollapsed">Logout</span>
        </button>
      </div>
      </div>
      <div style="width: 20%;" v-if="!isCollapsed"></div>
      <main class="mx-auto w-75 d-flex justify-content-center my-5 bg-secondary text-light rounded-3 p-3">
        <div class="main-content ">
            <h1 class="text-center my-2">ThinkSpeak Sensor Data Temperature Analysis</h1>
            <div class="bg-white rounded-3">
             <canvas ref="chartCanvas" class="w-100 h-100"></canvas>
            </div>
        </div>
      </main>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import Chart from 'chart.js/auto';
  import axios from 'axios';
  import { toast } from 'vue3-toastify'; // Import the toast function
  
// Fetch stored weights from the PHP backend
  export default {
    name: 'TemperatureChart',
    data() {
      return {
        isCollapsed: false,
        chartCanvas: ref(null)
      };
    },
    methods: {
      toggleCollapse() {
        this.isCollapsed = !this.isCollapsed;
      },
      async logout() {
        try {
          const response = await axios.post('http://localhost:8000/api/logout.php', {}, {
            withCredentials: true // Ensure cookies are sent with the request
          });

          if (response.data.status === 'success') {
            toast.success(response.data.message);
            // Optionally redirect to the login page or home page
            window.location.href = '/sign-in'; // Update the URL as needed
          } else {
            toast.error(response.data.message);
          }
        } catch (error) {
          toast.error('Logout failed: ' + error.message);
        }
      }
    },
    setup() {
    const chartCanvas = ref(null);
    let chartInstance = null;
    
    const fetchDataAndDrawChart = async () => {
      try {
        const response = await axios.get('https://api.thingspeak.com/channels/1703948/feeds.json?results=50');
        const entries = response.data.feeds;
        const timestamps = entries.map(entry => new Date(entry.created_at).toLocaleTimeString());
        const temperatures = entries.map(entry => parseFloat(entry.field1) + Math.floor(Math.random() * 50));
        if (chartInstance) {
          chartInstance.data.labels = timestamps;
          chartInstance.data.datasets[0].data = temperatures;
          chartInstance.update();
        } else {
          const ctx = chartCanvas.value.getContext('2d');
          chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
              labels: timestamps,
              datasets: [{
                label: 'Temperature',
                data: temperatures,
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1,
                fill: false
              }]
            },
            options: {
              scales: {
                x: { display: true, title: { display: true, text: 'Timestamp' } },
                y: { display: true, title: { display: true, text: 'Temperature' } }
              }
            }
          });
        }
      } catch (error) {
        toast.error('Error fetching data: ' + error.message);
        console.error('Error fetching data:', error);
      }
    };
    
    onMounted(() => {
      fetchDataAndDrawChart();
      setInterval(fetchDataAndDrawChart, 60000); // Update every minute,60s
    });
    
    return { chartCanvas };
  }
  };
  </script>
  
  <style scoped>
  .sidebar {
    transition: all 300ms ease-in-out;
    width: 250px;
    height: 100svh;
    background-color: #2e3b4e;
    color: #ffffff;
    display: flex;
    position: fixed;
    flex-direction: column;
    top: 0;
    left: 0;
  }
  
  .sidebar-header {
    font-size: 1.2rem;
    font-weight: bold;
    text-align: center;
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .nav-links {
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
  }
  
  .nav-links li {
    margin: 15px 0;
  }
  
  .nav-links a {
    text-decoration: none;
    color: #c1c1c1;
    display: flex;
    align-items: center;
    padding: 10px 20px;
    font-size: 1rem;
    transition: background-color 0.3s, color 0.3s;
  }
  
  .nav-links a:hover {
    background-color: #1e2a38;
    color: white;
  }
  
  .nav-links i {
    margin-right: 15px;
    font-size: 1.2rem;
  }
  
  .logout {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .logout a {
    text-decoration: none;
    color: #e74c3c;
    display: flex;
    align-items: center;
    font-size: 1rem;
  }
  
  .logout i {
    margin-right: 15px;
  }
  
  .collapsed {
    width: 80px;
  }
  
  .collapse-btn {
    background: none;
    border: none;
    color: #ecf0f1;
    font-size: 1.5em;
    cursor: pointer;
  }
  </style>
  