/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const excelRoutes = {
  path: '/excel',
  component: Layout,
  redirect: '/excel/export-excel',
  name: 'Excel',
  meta: {
    title: 'excel',
    icon: 'excel',
    permissions: ['view menu excel'],
  },
  children: [
    {
      path: 'upload-excel',
      component: () => import('@/views/excel/UploadExcel'),
      name: 'UploadExcel',
      meta: { title: 'uploadExcel' },
    },
  ],
};

export default excelRoutes;
