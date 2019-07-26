const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  size: state => state.app.size,
  device: state => state.app.device,
  userId: state => state.user.id,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  introduction: state => state.user.introduction,
  roles: state => state.user.roles,
  permissions: state => state.user.permissions,
  permission_routes: state => state.permission.routes,
  addRoutes: state => state.permission.addRoutes,
  changeChartSizeId: state => state.settings.chartResizeId,
  chartActiveId: state => state.settings.chartActiveId,
  changeChartLastUploadId: state => state.settings.chartLastUploadId,
  chartLoading: state => state.settings.chartLoading,
};

export default getters;
