<template>
  <div id="app">
    <div class="container">
      <div class="Chart__list">
        <div class="Chart">
          <h2>Weekly Breakdown</h2>
          <line-example></line-example>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LineExample from './LineChart.js'
export default {
  name: 'Example',
  components: {
    LineExample
  }
}
</script>