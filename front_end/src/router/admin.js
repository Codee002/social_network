import AdminLayout from "../layouts/AdminLayout"
import Test from "../pages/admin/TestPage"
import Card from "../pages/admin/CardPage"

const adminRoutes = [
  {
    path: "/admin",
    component: AdminLayout,
    children: [
      {
        path: "test",
        component: Test,
        name: "admin.test"
      },
      {
        path: "card",
        component: Card,
        name: "admin.card"
      }
    ]
  }
];


export default adminRoutes;
