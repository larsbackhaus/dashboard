import defaultSettings from '@/settings';
const { showSettings, tagsView, fixedHeader, sidebarLogo, theme } = defaultSettings;

const state = {
  theme: theme,
  showSettings: showSettings,
  tagsView: tagsView,
  fixedHeader: fixedHeader,
  sidebarLogo: sidebarLogo,
  chartResizeId: 0,
  chartActiveId: 0,
  chartLastUploadId: 0,
  chartLoading: false,
};

const mutations = {
  CHANGE_SETTING: (state, { key, value }) => {
    if (state.hasOwnProperty(key)) {
      state[key] = value;
    }
  },
  CHANGE_CHART_RESIZE_ID: (state, { id }) => {
    state.chartResizeId = id;
  },
  CHANGE_CHART_ACTIVE_ID: (state, { id }) => {
    state.chartActiveId = id;
  },
  CHANGE_CHART_LAST_UPLOAD_ID: (state, { id }) => {
    state.chartLastUploadId = id;
  },
  CHANGE_CHART_LOADING: (state, { status }) => {
    state.chartLoading = status;
  },
};

const actions = {
  changeSetting({ commit }, data) {
    commit('CHANGE_SETTING', data);
  },
  changeChartSizeId({ commit }, data) {
    commit('CHANGE_CHART_RESIZE_ID', data);
  },
  changeChartActiveId({ commit }, data) {
    commit('CHANGE_CHART_ACTIVE_ID', data);
  },
  changeChartLastUploadId({ commit }, data) {
    commit('CHANGE_CHART_LAST_UPLOAD_ID', data);
  },
  chartLoading({ commit }, data) {
    commit('CHANGE_CHART_LOADING', data);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};

