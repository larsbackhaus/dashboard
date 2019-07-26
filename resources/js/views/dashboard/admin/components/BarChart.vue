<template>
  <div :class="className" :style="{height:height,width:width}"/>
</template>

<script>
import echarts from 'echarts';
require('echarts/theme/macarons');
import { debounce } from '@/utils';

const animationDuration = 3000;

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
  mounted() {
    this.initChart();
    this.__resizeHandler = debounce(() => {
      if (this.chart) {
        this.chart.resize();
      }
    }, 100);
    window.addEventListener('resize', this.__resizeHandler);
    this.$store.dispatch('settings/changeChartSizeId', { id: -2 });
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    window.removeEventListener('resize', this.__resizeHandler);
    this.chart.dispose();
    this.chart = null;
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.setOptions(val);
      },
    },
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
  methods: {
    setOptions({ xAxisData, realCostData, ourCostData, savedData } = {}) {
      this.chart.setOption({
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'shadow',
          },
        },
        grid: {
          top: 10,
          left: '2%',
          right: '2%',
          bottom: 20,
          containLabel: true,
        },
        xAxis: [{
          type: 'category',
          data: xAxisData,
          axisTick: {
            alignWithLabel: true,
          },
        }],
        yAxis: [{
          type: 'value',
          axisTick: {
            show: false,
          },
        }],
        series: [{
          name: 'Our Cost',
          type: 'bar',
          stack: 'saved',
          barWidth: '70%',
          color: '#B4DCB7',
          data: ourCostData,
          animationDuration,
        }, {
          name: 'Saved',
          type: 'bar',
          stack: 'saved',
          barWidth: '70%',
          color: '#53ABAA',
          data: savedData,
          animationDuration,
        }],
      });
    },
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons');
      this.setOptions(this.chartData);
    },
  },
};
</script>
