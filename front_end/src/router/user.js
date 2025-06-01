import MainLayout from "../layouts/MainLayout";
import DashboardPage from "../pages/user/DashboardPage";
import FriendPage from "../pages/user/FriendPage";
import ProfilePage from "../pages/user/ProfilePage";
import FavoritePage from "../pages/user/FavoritePage";
import ConversationPage from "../pages/user/ConversationPage";
import NotFound from "../pages/errors/NotFound";

const userRoutes = [
  {
    path: "/",
    component: MainLayout,
    children: [
      { path: "home", component: DashboardPage, name: "home" },
      { path: "friend", component: FriendPage, name: "friend" },
      { path: "profile", component: ProfilePage, name: "profile" },
      { path: "favorite", component: FavoritePage, name: "favorite" },
      {
        path: "conversation",
        component: ConversationPage,
        name: "conversation",
      },
      { path: ':pathMatch(.*)*', component: NotFound, name: 'not-found' },
    ],
  },
];

export default userRoutes;
