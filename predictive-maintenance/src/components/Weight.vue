<script setup>
import { ref, shallowRef, computed, watch, nextTick, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import axios from 'axios';
import { toast } from 'vue3-toastify';

// Sidebar component logic
const isCollapsed = ref(false);
const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
};

// Logout functionality
const logout = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/logout.php', {}, {
      headers: {
        'Content-Type': 'application/json'
      },
      withCredentials: true
    });

    if (response.data.status === 'success') {
      toast.success(response.data.message);
      window.location.href = '/sign-in'; // Update the URL as needed
    } else {
      toast.error(response.data.message);
    }
  } catch (error) {
    toast.error('Logout failed: ' + error.message);
  }
};

// Weight tracking logic
const weights = ref([]);
const weightChartEl = ref(null);
const weightChart = shallowRef(null);
const weightInput = ref(0);
const currentWeight = computed(() => {
  return weights.value.sort((a, b) => b.date - a.date)[0] || { weight: 0 };
});

// Fetch stored weights from the PHP backend
const fetchWeights = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getweights.php', {
      headers: {
        'Content-Type': 'application/json'
      },
      withCredentials: true
    });

    if (response.data.status === 'success') {
      weights.value = response.data.weights.map(weight => ({
        ...weight,
        date: new Date(weight.created_at).getTime()
      }));
      toast.success('Weights fetched successfully!');
    } else {
      toast.error('Failed to fetch weights: ' + response.data.message);
    }
  } catch (error) {
    toast.error('Error fetching weights: ' + error.message);
  }
};

// Add weight and save it to the PHP backend
const addWeight = async () => {
  const newWeight = {
    weight: weightInput.value,
    date: new Date().getTime()
  };

  try {
    const response = await axios.post('http://localhost:8000/api/addweight.php', {
      weight: weightInput.value
    }, {
      headers: {
        'Content-Type': 'application/json'
      },
      withCredentials: true
    });

    if (response.data.status === 'success') {
      toast.success('Weight added successfully!');
      weights.value.push(newWeight);
      weightInput.value = 0;
    } else {
      toast.error(response.data.message);
    }
  } catch (error) {
    toast.error('Failed to add weight: ' + error.message);
  }
};

// Watch for changes in weights and update the chart accordingly
watch(weights, (newWeights) => {
  const ws = [...newWeights];

  if (weightChart.value) {
    weightChart.value.data.labels = ws
      .sort((a, b) => a.date - b.date)
      .map(weight => new Date(weight.date).toLocaleDateString())
      .slice(-7);

    weightChart.value.data.datasets[0].data = ws
      .sort((a, b) => a.date - b.date)
      .map(weight => weight.weight)
      .slice(-7);

    weightChart.value.update();
    return;
  }

  nextTick(() => {
    weightChart.value = new Chart(weightChartEl.value.getContext('2d'), {
      type: 'line',
      data: {
        labels: ws
          .sort((a, b) => a.date - b.date)
          .map(weight => new Date(weight.date).toLocaleDateString()),
        datasets: [
          {
            label: 'Weight',
            data: ws
              .sort((a, b) => a.date - b.date)
              .map(weight => weight.weight),
            backgroundColor: 'rgba(255, 105, 180, 0.2)',
            borderColor: 'rgba(255, 105, 180, 1)',
            borderWidth: 1,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  });
}, { deep: true });

// Fetch the stored weights when the component is mounted
onMounted(() => {
  fetchWeights();
});
</script>

<template>
  <div class="full-container d-flex gap-3 justify-content-between align-items-start position-relative">
    <!-- Sidebar -->
    <div class="sidebar" :class="{ collapsed: isCollapsed }">
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
            <i class="fas fa-thermometer-half"></i>
            <span v-if="!isCollapsed">Temperature</span>
          </router-link>
        </li>
        <li>
          <router-link to="/humidity">
            <i class="fas fa-water"></i>
            <span v-if="!isCollapsed">Humidity</span>
          </router-link>
        </li>
        <li>
          <router-link to="/weight">
            <i class="fas fa-weight-hanging"></i>
            <span v-if="!isCollapsed">Weight</span>
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

    <!-- Main content -->
    <main class="mx-auto w-75 d-flex justify-content-center my-5 bg-secondary text-light rounded-3 p-3">
      <div class="w-100">
        <h1>Weight Tracker</h1>

        <div class="current text-secondary">
          <span>{{ currentWeight.weight }}</span>
          <small>Current Weight (kg)</small>
        </div>

        <form @submit.prevent="addWeight">
          <input 
            type="number"
            step="0.1"
            v-model="weightInput" />
          <input	
            type="submit"
            value="Add weight" />
        </form>

        <div v-if="weights && weights.length > 0">
          <h2 class="text-center">Last 7 days</h2>
          <div class="canvas-box h-100">
            <canvas ref="weightChartEl"></canvas>
          </div>

          <div class="weight-history">
            <h2  class="text-center">Weight History</h2>
            <ul>
              <li v-for="(weight, index) in weights" :key="index">
                <span>{{ weight.weight }}kg</span>
                <small>{{ new Date(weight.date).toLocaleDateString() }}</small>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
	font-family: 'montserrat', sans-serif;
}
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
h1 {
	font-size: 2em;
	text-align: center;
	margin-bottom: 2rem;
}

h2 {
	margin-bottom: 1rem;
	color: #888;
	font-weight: 400;
}

.current {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;

	width: 200px;
	height: 200px;
	
	text-align: center;
	background-color: white;
	border-radius: 999px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
	border: 5px solid hwb(330 41% 0%);
	
	margin: 0 auto 2rem;
}

.current span {
	display: block;
	font-size: 2em;
	font-weight: bold;
	margin-bottom: 0.5rem;
}

.current small {
	color: #888;
	font-style: italic;
}

form {
	display: flex;
	margin-bottom: 2rem;
	border: 1px solid #AAA;
	border-radius: 0.5rem;
	overflow: hidden;
	transition: 200ms linear;
}

form:focus-within,
form:hover {
	border-color: hotpink;
	border-width: 2px;
}

form input[type="number"] {
	appearance: none;
	outline: none;
	border: none;
	background-color: white;

	flex: 1 1 0%;
	padding: 1rem 1.5rem;
	font-size: 1.25rem;
}

form input[type="submit"] {
	appearance: none;
	outline: none;
	border: none;
	cursor: pointer;
	background-color: hotpink;

	padding: 0.5rem 1rem;

	color: white;
	font-size: 1.25rem;
	font-weight: 700;
	transition: 200ms linear;
	border-left: 3px solid transparent;
}

form input[type="submit"]:hover {
	background-color: white;
	color: hotpink;
	border-left-color: hotpink;
}

.canvas-box {
	width: 100%;
	background-color: white;
	padding: 1rem;
	border-radius: 0.5rem;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
	margin-bottom: 2rem;
}

.weight-history ul {
	list-style: none;
	padding: 0;
	margin: 0;
}

.weight-history ul li {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 0.5rem;
	cursor: pointer;
}

.weight-history ul li:nth-child(even) {
	background-color: #dfdfdf;
}

.weight-history ul li:hover {
	background-color: #f8f8f8;
}

.weight-history ul li:last-of-type {
	border-bottom: none;
}

.weight-history ul li span {
	display: block;
	font-size: 1.25rem;
	font-weight: 700;
	margin-right: 1rem;
}

.weight-history ul li small {
	color: #888;
	font-style: italic;
}
</style>
