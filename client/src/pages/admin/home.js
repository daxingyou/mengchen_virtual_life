import { myTools} from './index.js'
import MyToastr from '../../components/MyToastr.vue'

new Vue({
  el: '#app',
  components: {
    MyToastr,
  },
  data: {
    adminHomeApi: '/admin/api/home',
    hello: 'hi',
  },

  created: function () {
    //
  },

  mounted: function () {
    let _self = this
    let toastr = this.$refs.toastr

    myTools.axiosInstance.get(this.adminHomeApi)
      .then(function (res) {
        myTools.msgResolver(res, toastr)
        _self.hello = res.data.message
      })
  },
})