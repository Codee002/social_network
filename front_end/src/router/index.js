import { createRouter, createWebHistory } from "vue-router";
import adminRoutes from "./admin";
import authRoutes from "./auth";

const routes = [...adminRoutes, ...authRoutes];

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router;
