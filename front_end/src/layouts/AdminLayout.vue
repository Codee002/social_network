<template>
  <router-view></router-view>
</template>

// <script >
// import { reactive, ref } from "vue";
// import axios from "axios";
// export default {
//   setup() {
//     const test = ref(0);
//     const users = reactive([]);
//     function testConsole() {
//       console.log(users);
//     }
//     return {
//       test,
//       users,
//       testConsole,
//     };
//   },

//   created()
//   {
//     axios
//     .get("http://127.0.0.1:8000/api/test")
//     .then( res => {
//       console.log("reactive: " , this.users);
//       this.users = res.data;
//       console.log("reactive: " , this.users);
//     })
//     .catch(error => console.log(error));
//   },
  
// };
// </script>
