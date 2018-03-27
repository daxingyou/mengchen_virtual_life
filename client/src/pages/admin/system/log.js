import '../index.js'
import MyVuetable from '../../../components/MyVuetable.vue'
import FilterBar from '../../../components/MyFilterBar.vue'
import DetailRow from './components/DetailRow.vue'

Vue.component('detail-row', DetailRow)

new Vue({
  el: '#app',
  components: {
    MyVuetable,
    FilterBar,
  },
  data: {
    eventHub: new Vue(),
    tableUrl: '/admin/api/system/log',
    tableFields: [
      {
        name: 'id',
        title: 'ID',
        sortField: 'id',
      },
      {
        name: 'user.account',
        title: '用户账号',
        sortField: 'user_id',
      },
      {
        name: 'uri',
        title: 'URI路径',
        sortField: 'uri',
      },
      {
        name: 'method',
        title: '操作方法',
        sortField: 'method',
      },
      {
        name: 'description',
        title: '描述',
      },
      {
        name: 'created_at',
        title: '时间',
      },
    ],
    detailRowComponent: 'detail-row',
  },
})