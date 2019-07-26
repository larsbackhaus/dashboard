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
    autoResize: {
      type: Boolean,
      default: true,
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
      sidebarElm: null,
    };
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
  mounted() {
    this.initChart();
    if (this.autoResize) {
      this.__resizeHandler = debounce(() => {
        if (this.chart) {
          this.chart.resize();
        }
      }, 100);
      window.addEventListener('resize', this.__resizeHandler);
    }

    // Monitor the sidebar changes
    this.sidebarElm = document.getElementsByClassName('sidebar-container')[0];
    this.sidebarElm && this.sidebarElm.addEventListener('transitionend', this.sidebarResizeHandler);
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    if (this.autoResize) {
      window.removeEventListener('resize', this.__resizeHandler);
    }

    this.sidebarElm && this.sidebarElm.removeEventListener('transitionend', this.sidebarResizeHandler);

    this.chart.dispose();
    this.chart = null;
  },
  computed: {
    resizeChartId() {
      return this.$store.getters.changeChartSizeId;
    },
  },
  methods: {
    sidebarResizeHandler(e) {
      if (e.propertyName === 'width') {
        this.__resizeHandler();
      }
    },
    setOptions({ xAxisData, realCostData, ourCostData, savedData } = {}) {
      this.chart.setOption({
        xAxis: {
          data: xAxisData,
          boundaryGap: false,
          axisTick: {
            show: false,
          },
        },
        grid: {
          left: 10,
          right: 10,
          bottom: 20,
          top: 30,
          containLabel: true,
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross',
          },
          padding: [5, 10],
        },
        yAxis: {
          axisTick: {
            show: false,
          },
        },
        legend: {
          data: ['Real Cost', 'Our Cost', 'Saved'],
        },
        series: [{
          name: 'Real Cost', itemStyle: {
            normal: {
              color: '#724A6C',
              lineStyle: {
                color: '#724A6C',
                width: 2,
              },
              areaStyle: {
                color: '#724A6C',
              },
            },
          },
          smooth: true,
          type: 'line',
          data: realCostData,
          animationDuration: 2800,
          animationEasing: 'cubicInOut',
        },
        {
          name: 'Our Cost',
          smooth: true,
          type: 'line',
          itemStyle: {
            normal: {
              color: '#53ABAA',
              lineStyle: {
                color: '#53ABAA',
                width: 2,
              },
              areaStyle: {
                color: '#53ABAA',
              },
            },
          },
          data: ourCostData,
          animationDuration: 2800,
          animationEasing: 'quadraticOut',
        },
        {
          name: 'Saved',
          smooth: true,
          type: 'line',
          itemStyle: {
            normal: {
              color: '#B4DCB7',
              lineStyle: {
                color: '#B4DCB7',
                width: 2,
              },
              areaStyle: {
                color: '#B4DCB7',
              },
            },
          },
          data: savedData,
          animationDuration: 2800,
          animationEasing: 'quadraticOut',
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
