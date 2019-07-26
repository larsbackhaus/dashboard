<template>
    <div class="dashboard-editor-container">
        <div v-if="!loading" class="preloader">
            <i class="preloader-icon el-icon-loading"/>
        </div>
        <div v-if="loading" class="files-panel">
            <el-select v-model="selectChartId" style="width: 240px" class="filter-item">
              <el-option v-for="item in chartData" :key="item.id" :label="item.fileName" :value="item.id" />
            </el-select>
            <el-button type="success" @click="chartWidjetShow" style="background-color: #4FAE3E; border-color: #4FAE3E;">
              <i class="el-icon-plus"/> Add Chart
            </el-button>
            <input style="display:none;" ref="excel-upload-input" class="excel-upload-input" type="file" accept=".xlsx, .xls, .csv" @change="handleClick"/>
            <el-button class="upload-data-file-button" type="primary" @click="handleUpload" style="background-color: #F07D23; border-color: #F07D23;">
              <i class="el-icon-upload"/> Upload data file
            </el-button>
        </div>
        <div v-if="loading" class="grid-container">
            <div class="grid-sub-container" @click="chartContainerClick" data-is-sub-container="">
                <VueDragResize
                        class="chart-widget"
                        v-for="edElem in chartData"
                        :key="edElem.id"
                        v-show="Boolean(edElem.visible)"
                        @resizestop="chartWidgetResize"
                        @dragstop="chartWidgetDragging"
                        :vdrWidjetID="edElem.id"
                        :x="Number(edElem.ch_left)" :y="Number(edElem.ch_top)"
                        :w="Number(edElem.ch_width)" :minw="250" :h="Number(edElem.ch_height)" :minh="200"
                        :parentLimitation="true"
                        :z="Number(chartActiveId == edElem.id)"
                        :snapToGrid="true"
                        :gridX="25" :gridY="25"
                        :isActive="Boolean(chartActiveId == edElem.id)"
                        :preventActiveBehavior="true">
                    <div class="bar-chart__title" @click.stop.self="chartTitleClick" :data-chart-id="edElem.id">
                        {{edElem.fileName}}
                        <div class="bar-chart__title__buttons-cont">
                            <span v-show="!(chartActiveId == edElem.id)" @click.stop.self="chartModeChange" data-mode-number="1" data-mode="Diagram" :data-chart-id="edElem.id" class="bar-chart__title__button bar-chart__title__button_mode" :class="{'bar-chart__title__button_mode_active' : edElem.mode === 'Diagram'}">D</span>
                            <span v-show="!(chartActiveId == edElem.id)" @click.stop.self="chartModeChange" data-mode-number="2" data-mode="Linear" :data-chart-id="edElem.id" class="bar-chart__title__button bar-chart__title__button_mode" :class="{'bar-chart__title__button_mode_active' : edElem.mode === 'Linear'}">L</span>
                            <span v-show="!(chartActiveId == edElem.id)" @click.stop.self="chartModeChange" data-mode-number="3" data-mode="Radian" :data-chart-id="edElem.id" class="bar-chart__title__button bar-chart__title__button_mode" :class="{'bar-chart__title__button_mode_active' : edElem.mode === 'Radian'}">R</span>
                            <i v-show="!(chartActiveId == edElem.id)" @click.stop.self="chartClose" :data-chart-id="edElem.id" class="bar-chart__title__button bar-chart__title__button_close el-icon-close"></i>
                        </div>
                    </div>
                    <div class="chart-sub-cont">
                        <line-chart v-if="edElem.mode === 'Linear'" :chart-id="edElem.id" :chart-data="getChartDataForId(edElem.id)"/>
                        <bar-chart v-else-if="edElem.mode === 'Diagram'" :chart-id="edElem.id" :chartData="getChartDataForId(edElem.id)"/>
                        <pie-chart v-else-if="edElem.mode === 'Radian'" :chart-id="edElem.id" :chartData="getChartDataForId(edElem.id)"/>
                    </div>
                </VueDragResize>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VueDragResize from './components/VueDragResize';
import LineChart from './components/LineChart';
import BarChart from './components/BarChart';
import PieChart from './components/PieChart';

export default {
  name: 'DashboardAdmin',
  components: {
    LineChart,
    BarChart,
    VueDragResize,
    PieChart,
  },
  data() {
    return {
      chartData: [],
      selectChartId: 77,
      lineChartData: {
        xAxisData: [],
        realCostData: [],
        ourCostData: [],
        savedData: [],
      },
    };
  },
  computed: {
    loading() {
      return this.$store.getters.chartLoading;
    },
    chartActiveId() {
      return this.$store.getters.chartActiveId;
    },
    changeChartLastUploadId() {
      return this.$store.getters.changeChartLastUploadId;
    },
  },
  methods: {
    handleUpload() {
      this.$refs['excel-upload-input'].click();
    },
    handleClick(e) {
      const files = e.target.files;
      const rawFile = files[0]; // only use files[0]
      if (!rawFile) {
        return;
      }
      this.upload(rawFile);
    },
    upload(rawFile) {
      const self = this;
      this.$refs['excel-upload-input'].value = null; // fix can't select the same excel

      const fileData = new FormData();
      fileData.append('user_id', this.$store.getters.userId);
      fileData.append('upload_file', rawFile);

      axios.post('api/excel/store', fileData, { headers: { 'Content-Type': 'multipart/form-data' }}).then(function (response) {
        self.$store.dispatch('settings/changeChartLastUploadId', { id: (Number.isInteger(response.data)) ? response.data : -1 });
      }).catch(function (response) {
        console.log('fail', response);
      });
    },
    chartContainerClick(e) {
      if (e.target.dataset.isSubContainer !== undefined) {
        this.$store.dispatch('settings/changeChartActiveId', { id: 0 });
      }
    },
    chartTitleClick(e) {
      const cwId = e.target.dataset.chartId;
      this.$store.dispatch('settings/changeChartActiveId', { id: cwId });
    },
    chartModeChange(e) {
      const cwId = parseInt(e.target.dataset.chartId);
      const newMode = e.target.dataset.mode;

      for (let i = 0; i < this.chartData.length; i++) {
        if (this.chartData[i].id === cwId) {
          this.chartData[i].mode = newMode;
          break;
        }
      }

      const mode = parseInt(e.target.dataset.modeNumber);

      axios.put('api/excel/update-chart-mode/' + cwId, {
        mode: mode,
      }).then(function (response) {
      }).catch(function (response) {
        console.log('fail', response);
      });
    },
    chartVisibleSet(id, visible) {
      for (let i = 0; i < this.chartData.length; i++) {
        if (this.chartData[i].id === id) {
          this.chartData[i].visible = visible;
          break;
        }
      }
      axios.put('api/excel/update-chart-visible/' + id, {
        visible: visible,
      }).then(function (response) {
      }).catch(function (response) {
        console.log('fail', response);
      });
    },
    chartClose(e) {
      const cwId = parseInt(e.target.dataset.chartId);
      this.chartVisibleSet(cwId, 0);
    },
    chartWidjetShow() {
      this.chartVisibleSet(this.selectChartId, 1);
      this.$store.dispatch('settings/changeChartActiveId', { id: this.selectChartId });
      this.$store.dispatch('settings/changeChartSizeId', { id: this.selectChartId });
    },
    chartPosUpdate(rect, id) {
      axios.put(`api/excel/update-chart-pos/${id}`, {
        ch_left: rect.left,
        ch_top: rect.top,
        ch_width: rect.width,
        ch_height: rect.height,
      }).then(function (response) {
      }).catch(function (response) {
        console.log('fail', response);
      });
    },
    chartWidgetDragging(newRect, cwId) {
      if (cwId !== -1) {
        this.chartPosUpdate(newRect, cwId);
      }
    },
    chartWidgetResize(newRect, cwId) {
      this.chartPosUpdate(newRect, cwId);
      this.$store.dispatch('settings/changeChartSizeId', { id: cwId });
    },
    getChartDataForId(id) {
      const self = this;
      const dataProcessed = {
        xAxisData: [],
        realCostData: [],
        ourCostData: [],
        savedData: [],
      };

      for (let i = 0; i < self.chartData.length; i++) {
        if (self.chartData[i].id === id) {
          const dataCount = self.chartData[i].csv.length;
          for (let j = 1; j < dataCount; j++) {
            dataProcessed.xAxisData.push(self.chartData[i].csv[j][0]);
            dataProcessed.realCostData.push(self.chartData[i].csv[j][1]);
            dataProcessed.ourCostData.push(self.chartData[i].csv[j][2]);
            dataProcessed.savedData.push(self.chartData[i].csv[j][3]);
          }
          break;
        }
      }
      return dataProcessed;
    },
    getChartAllData() {
      const self = this;
      const dataProcessed = {
        xAxisData: [],
        realCostData: [],
        ourCostData: [],
        savedData: [],
      };

      for (let i = 0; i < self.chartData.length; i++) {
        const dataCount = self.chartData[i].csv.length;
        for (let j = 1; j < dataCount; j++) {
          dataProcessed.xAxisData.push(self.chartData[i].csv[j][0]);
          dataProcessed.realCostData.push(self.chartData[i].csv[j][1]);
          dataProcessed.ourCostData.push(self.chartData[i].csv[j][2]);
          dataProcessed.savedData.push(self.chartData[i].csv[j][3]);
        }
      }

      return dataProcessed;
    },
    loadData() {
      const self = this;
      axios.get('api/excel/get-all/' + this.$store.getters.userId).then(function (response) {
        self.chartData = response.data.data;
        // console.log(self.chartData);
        self.selectChartId = self.chartData[0].id;
      }).catch(function (response) {
        console.log('fail', response);
      });

      setTimeout(function() {
        window.Vue.$store.dispatch('settings/changeChartSizeId', { id: -11 });
        window.Vue.$store.dispatch('settings/chartLoading', { status: true });
      }, 1000);
    },
  },
  created() {
    this.loadData();
  },
  watch: {
    changeChartLastUploadId(luId) {
      const self = this;
      if (luId !== -1) {
        axios.get('api/excel/get/' + luId).then(function (response) {
          self.chartData.push(response.data.data[0]);
          self.$store.dispatch('settings/changeChartSizeId', { id: -10 });
        }).catch(function (response) {
          console.log('fail', response);
        });
      }
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
    .dashboard-editor-container {
        padding: 16px;
        background-color: rgb(255, 255, 255);

        .chart-wrapper {
            position: relative;
            background: #fff;
            padding: 16px 16px 0;
            margin-bottom: 16px;
        }

        .chart-widget {
            background-color: white;
            border: 1px solid #505050;
        }

        .chart-sub-cont {
            overflow: hidden;
            height: calc(100% - 28px);
            padding-top: 5px;
        }
    }

    .components-container {
        position: relative;
        height: 100vh;
    }

    .sub-container {
        height: 200px;
    }

    .files-panel {
        margin-bottom: 16px;
        background-color: white;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .bar-chart__title {
        padding: 5px 0;
        text-align: center;
        color: #4FAE3E;
        background-color: #EEEEEE;
        width: calc(100% - 1px);

        .bar-chart__title__resize-cont {
            display: flex;
            float: left;
        }

        .bar-chart__title__buttons-cont {
            display: flex;
            float: right;

            .bar-chart__title__button {
              margin: 0 4px;
              cursor: pointer;
              color: black;
            }

            .bar-chart__title__button_mode {
                padding: 0 5px;
                border-radius: 50%;
                transition: 0.3s background-color;
            }

            .bar-chart__title__button_mode:hover {
                color: #4FAE3E;
            }

            .bar-chart__title__button_mode_active {
                font-weight: 800;
                color: #F07D23;
            }

            .bar-chart__title__button_close {
              color: #EA5441;
              display: flex;
              align-items: center;
            }
        }
    }

    .grid-container {
      background-color: white;
    }

    .grid-sub-container {
        position: relative;
        width: 100%;
        min-height: 1000px;
        background: linear-gradient(-90deg, rgba(0, 0, 0, 0.01) 1px, transparent 1px) 0% 0% / 25px 25px, linear-gradient(rgba(0, 0, 0, 0.01) 1px, transparent 1px) 0% 0% / 25px 25px;
    }

    .preloader {
        margin-bottom: 16px;
        background-color: white;
        padding: 15px;
        height: calc(100vh - 120px);
        display: flex;
        justify-content: center;
        align-items: center;

        .preloader-icon {
          font-size: 50px;
        }
    }

    .upload-data-file-button {
        float: right;
    }
</style>
