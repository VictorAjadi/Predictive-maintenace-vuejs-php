<template>
    <div class="container">
       <TemperatureChart />
       <HumidityChart />
       <Weight />
    </div>
  </template>
  
  <script>
    import TemperatureChart from '../components/TemperatureChart.vue'
    import HumidityChart from '../components/HumidityChart.vue'
    import Weight from '../components/Weight.vue'
    export default { 
        name: 'Dashboard'
    };
  </script>
  <style scoped>
     .container {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
</style>