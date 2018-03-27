<template>
    <div @click="onClick">
        <div class="">
            <label>浏览器版本:&nbsp;&nbsp;</label>
            <span>{{rowData.user_agent}}</span>
        </div>
        <div class="">
            <label>操作数据:&nbsp;&nbsp;</label>
            <span>{{rowData.data}}</span>
        </div>
    </div>
</template>

<script>
    export default {
      props: {
        rowData: {
          type: Object,
          required: true,
        },
        rowIndex: {
          type: Number,
        },
      },
      methods: {
        onClick () {
          //console.log('my-detail-row: on-click', event.target)
        },
      },
    }
</script>