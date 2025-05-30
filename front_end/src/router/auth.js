import AuthLayout from "../layouts/AuthLayout";
import Login from "../pages/auth/LoginPage";
import Register from "../pages/auth/RegisterPage";
import Forgot from "../pages/auth/ForgotPage";

const authRoutes = [
  {
    path: "/auth",
    component: AuthLayout,
    children: [
      {
        path: "login",
        component: Login,
        name: "auth.login",
      },
      {
        path: "register",
        component: Register,
        name: "auth.register",
      },
      {
        path: "forgot",
        component: Forgot,
        name: "auth.forgot",
      },
    ],
  },
];

export default authRoutes;
