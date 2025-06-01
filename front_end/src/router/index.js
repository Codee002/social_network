import { createRouter, createWebHistory } from "vue-router";
import adminRoutes from "./admin";
import authRoutes from "./auth";
import userRoutes from "./user";

const routes = [...adminRoutes, ...authRoutes, ...userRoutes];

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router;
