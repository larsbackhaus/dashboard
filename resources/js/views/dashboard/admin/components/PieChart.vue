<template>
  <div :class="className" :style="{height:height,width:width}"/>
</template>

<script>
import echarts from 'echarts';
require('echarts/theme/macarons'); // echarts theme
import { debounce } from '@/utils';

export default {
  props: {
    className: {
      type: String,
      default: 'chart',
    },
    width: {
      type: String,
      default: '100%',
    },
    height: {
      type: String,
      default: '100%',
    },
    chartData: {
      type: Object,
      required: true,
    },
    chartId: {
      type: Number,
      default: -1,
    },
  },
  data() {
    return {
      chart: null,
    };
  },
  watch: {
    resizeChartId(newChartChangeSizeID) {
      if ((newChartChangeSizeID <= -2 || newChartChangeSizeID === this.chartId) && this.chart) {
        this.chart.resize();
        if (newChartChangeSizeID > -2) {
          this.$store.dispatch('settings/changeChartSizeId', { id: 0 });
        }
      }
    },
  },
  computed: {
    resizeChartId() {
      return this.$store.getters.changeChartSizeId;
    },
  },
  mounted() {
    this.initChart();
    this.__resizeHandler = debounce(() => {
      if (this.chart) {
        this.chart.resize();
      }
    }, 100);
    window.addEventListener('resize', this.__resizeHandler);
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    window.removeEventListener('resize', this.__resizeHandler);
    this.chart.dispose();
    this.chart = null;
  },
  methods: {
    arraySum(array) {
      let sum = 0;
      for (let i = 0; i < array.length; i++) {
        sum += parseInt(array[i]);
      }
      return sum;
    },
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons');

      this.chart.setOption({
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b} : {c} ({d}%)',
        },
        legend: {
          left: 'center',
          bottom: '10',
          data: ['Our Cost', 'Real Cost', 'Saved'],
        },
        calculable: true,
        series: [
          {
            name: 'Consumption and savings',
            type: 'pie',
            roseType: 'radius',
            radius: ['20%', '70%'],
            center: ['50%', '45%'],
            data: [
              { value: this.arraySum(this.chartData.ourCostData), name: 'Our Cost' },
              { value: this.arraySum(this.chartData.realCostData), name: 'Real Cost' },
              { value: this.arraySum(this.chartData.savedData), name: 'Saved' },
            ],
            animationEasing: 'cubicInOut',
            animationDuration: 1600,
          },
        ],
      });
    },
  },
};
</script>
