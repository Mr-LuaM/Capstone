import { createRouter, createWebHistory } from "vue-router";
import Solo from "../layouts/Solo";
import Main from "../layouts/Main";
import HomeView from "../views/HomeView.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/admin/applicants",
    name: "caManageApplicants",
    component: Main,
    children: [
      {
        path: "",
        component: () =>
          import("../views/Admin/centralAdmin/caManageApplicants.vue"),
      },
    ],
  },
  {
    path: "/admin/stations",
    name: "caManageStation",
    component: Main,
    children: [
      {
        path: "",
        component: () =>
          import("../views/Admin/centralAdmin/caManageStation.vue"),
      },
    ],
  },

  {
    path: "/application",
    name: "application",
    component: Solo,
    children: [
      {
        path: "",
        component: () => import("../views/Registration/ApplicationForm.vue"),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
