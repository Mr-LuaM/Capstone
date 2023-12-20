import { createRouter, createWebHistory } from "vue-router";
import { jwtDecode as jwt_decode } from "jwt-decode";
import Solo from "../layouts/Solo";
import Main from "../layouts/Main";
import Station from "../layouts/Station";
import StudNav from "../layouts/StudNav";
import HomeView from "../views/HomeView.vue";
import createAccount from "../views/CreateAccount.vue";
import Teacher from "../layouts/Teacher.vue";
import store from "@/store";

const routes = [
  {
    path: "/unauthorized",
    name: "unauthorized",
    component: () => import("../views/404.vue"),
  },
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/login",
    name: "login",
    component: HomeView,
  },
  {
    path: "/createAccount",
    name: "createAccount",
    component: createAccount,
  },
  {
    path: "/auth/verify/:verificationCode",
    name: "verify-account",
    component: HomeView,
  },

  {
    path: "/admin",
    component: Main,
    meta: { requiredRoles: ["2"] },
    children: [
      {
        path: "dashboard",
        name: "caDashboard",
        component: () => import("../views/Admin/centralAdmin/caDashboard.vue"),
      },
      {
        path: "applicants",
        name: "caManageApplicants",
        component: () =>
          import("../views/Admin/centralAdmin/caManageApplicants.vue"),
      },
      {
        path: "accounts",
        name: "caManageAccounts",
        component: () =>
          import("../views/Admin/centralAdmin/caManageAccounts.vue"),
      },
      {
        path: "stations",
        name: "caManageStation",
        component: () =>
          import("../views/Admin/centralAdmin/caManageStation.vue"),
      },
      {
        path: "courses",
        name: "caManageCourses",
        component: () =>
          import("../views/Admin/centralAdmin/caManageCourse.vue"),
      },
      {
        path: "announcements",
        name: "caAnnouncement",
        component: () =>
          import("../views/Admin/centralAdmin/caAnnouncement.vue"),
      },
      {
        path: "history",
        name: "history",
        component: () =>
          import("../views/Admin/centralAdmin/caApplicantHistory.vue"),
      },
      {
        path: "edit-accounts",
        name: "caEditAccounts",
        component: () =>
          import("../views/Admin/centralAdmin/caEditAccount.vue"),
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
        beforeEnter: (to, from, next) => {
          // Check the enrollment status
          if (store.state.isEnrollmentOpen) {
            next(); // Allow navigation to the route
          } else {
            // Enrollment is closed, you can redirect to another route or display a message
            next("/enrollment-closed"); // Redirect to a "closed" page
          }
        },
      },
    ],
  },
  {
    path: "/enrollment-closed",
    name: "enrollment-closed",
    children: [
      {
        path: "",
        component: () => import("../views/404.vue"),
        beforeEnter: (to, from, next) => {
          // Check the enrollment status
          if (!store.state.isEnrollmentOpen) {
            next(); // Allow navigation to the route
          } else {
            // Enrollment is closed, you can redirect to another route or display a message
            next("/application"); // Redirect to a "closed" page
          }
        },
      },
    ],
  },

  {
    path: "/station",
    component: Station,
    meta: { requiredRoles: ["3"] },
    children: [
      {
        path: "dashboard",
        name: "saDashboard",
        component: () => import("../views/Admin/StationAdmin/saDashboard.vue"),
      },
      {
        path: "students",
        name: "saManageApplicants",
        component: () =>
          import("../views/Admin/StationAdmin/saManageStudents.vue"),
      },
      {
        path: "Teachers",
        name: "saManageAccounts",
        component: () =>
          import("../views/Admin/StationAdmin/saManageTeachers.vue"),
      },
      {
        path: "stations",
        name: "saManageStation",
        component: () =>
          import("../views/Admin/StationAdmin/saManageStation.vue"),
      },
      {
        path: "courses",
        name: "saManageCourses",
        component: () =>
          import("../views/Admin/StationAdmin/saManageCourse.vue"),
      },
      {
        path: "schedule",
        name: "saManageCourses",
        component: () => import("../views/Admin/StationAdmin/saSchedule.vue"),
      },
      {
        path: "announcements",
        name: "saAnnouncement",
        component: () =>
          import("../views/Admin/StationAdmin/saAnnouncement.vue"),
      },
      {
        path: "edit-accounts",
        name: "saEditAccounts",
        component: () =>
          import("../views/Admin/StationAdmin/saEditAccount.vue"),
      },
    ],
  },
  {
    path: "/teacher",
    component: Teacher,
    meta: { requiredRoles: ["4"] },
    children: [
      {
        path: "dashboard",
        name: "tDashboard",
        component: () => import("../views/Teachers/tDashboard.vue"),
      },
      {
        path: "students",
        name: "tStudents",
        component: () => import("../views/Teachers/tStudents.vue"),
      },
      {
        path: "schedule",
        name: "tSchedule",
        component: () => import("../views/Teachers/t.Schedule.vue"),
      },
      {
        path: "manage-exams",
        name: "tmanageexams",
        component: () => import("../views/Teachers/tManageExams.vue"),
      },
      {
        path: "manage-exams/:id",
        name: "teditexams",
        component: () => import("../views/Teachers/tManageExams.vue"),
      },
      {
        path: "announcements",
        name: "tAnnouncement",
        component: () => import("../views/Teachers/t.Announcements.vue"),
      },
      {
        path: "edit-accounts",
        name: "tEditAccounts",
        component: () => import("../views/Teachers/tEditAccount.vue"),
      },
      {
        path: "exams",
        name: "tExam",
        component: () => import("../views/Teachers/t.Exams.vue"),
      },
    ],
  },
  {
    path: "/student",
    component: StudNav,
    meta: { requiredRoles: ["5"] },
    children: [
      {
        path: "dashboard",
        name: "sDashboard",
        component: () => import("../views/Student/Dashboard.vue"),
      },
      {
        path: "announcements",
        name: "sAnnouncement",
        component: () => import("../views/Student/announcement.vue"),
      },
      {
        path: "edit-accounts",
        name: "sEditAccounts",
        component: () => import("../views/Student/Profile.vue"),
      },
      {
        path: "exams",
        name: "sexams",
        component: () => import("../views/Student/exams.vue"),
      },
      {
        path: "/take-exam/:examId",
        name: "takeExam",
        component: () => import("../views/Student/takeExam.vue"),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const requiredRoles = to.meta.requiredRoles;

  // Check if the route requires roles
  if (requiredRoles) {
    const userRoles = getUserRoles();

    // Check if the user is logged in
    if (!userRoles.length) {
      // Redirect to the login page or handle the scenario where the user is not logged in
      next("/login");
      return;
    }

    // Check if the user has the required roles
    if (!hasRequiredRoles(userRoles, requiredRoles)) {
      // Redirect to unauthorized page or show an error message
      next("/unauthorized");
      return;
    }
  }

  // Continue to the next route
  next();
});

function getUserRoles() {
  const token = localStorage.getItem("jwt_token");

  if (!token) {
    console.error("JWT token not found.");
    return [];
  }

  try {
    const decodedToken = jwt_decode(token);

    // Ensure that userRoles is an array
    return Array.isArray(decodedToken.role)
      ? decodedToken.role
      : [decodedToken.role];
  } catch (error) {
    console.error("Error decoding token:", error);
    return [];
  }
}

function hasRequiredRoles(userRoles, requiredRoles) {
  // Check if the user has at least one of the required roles
  return requiredRoles.some((role) => userRoles.includes(role));
}

export default router;
