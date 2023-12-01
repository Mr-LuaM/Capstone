// store/index.js
import createPersistedState from "vuex-persistedstate";
import axios from "axios";
import { createStore } from "vuex";

export default createStore({
  state: {
    isLoggedIn: false,
    userId: null,
    userEmail: null,
    userName: null,
    userProfile: null,
    role: null,
  },
  mutations: {
    setLoggedIn(state, isLoggedIn) {
      state.isLoggedIn = isLoggedIn;
      console.log("isLoggedIn:", isLoggedIn);
    },
    setUserId(state, userId) {
      state.userId = userId;
      console.log("Setting userId:", userId);
    },
    setUserEmail(state, userEmail) {
      state.userEmail = userEmail;
      console.log("Setting userEmail:", userEmail);
    },
    setUserName(state, userName) {
      state.userName = userName;
      console.log("Setting userName:", userName);
    },
    setUserProfile(state, userProfile) {
      state.userProfile = userProfile;
      console.log("Setting userProfile:", userProfile);
    },
    setRole(state, role) {
      state.role = role;
      console.log("Setting role:", role);
    },
  },
  actions: {
    async fetchUserDetails({ commit, getters }) {
      try {
        const userId = getters.userId;
        const role = getters.role;
        const response = await axios.get(`/getUserDetails/${role}/${userId}`);

        if (response.data) {
          const { fullName, profilePicture } = response.data;
          commit("setUserName", response.data.fullName);
          commit("setUserProfile", response.data.profilePicture);
        }
      } catch (error) {
        console.error("Error fetching user details:", error);
      }
    },
    login({ commit }, { userId, userEmail, userRole }) {
      commit("setLoggedIn", true);
      commit("setUserId", userId);
      commit("setUserEmail", userEmail);
      commit("setRole", userRole);

      console.log(
        "User logged in. userId:",
        userId,
        "userEmail:",
        userEmail,
        "userRole:",
        userRole
      );
    },
    logout({ commit }) {
      commit("setLoggedIn", false);
      commit("setUserId", null);
      commit("setUserEmail", null);
      commit("setRole", null);

      console.log("User logged out.");
    },
  },
  getters: {
    isLoggedIn: (state) => state.isLoggedIn,
    userId: (state) => state.userId,
    userEmail: (state) => state.userEmail,
    userName: (state) => state.userName,
    userProfile: (state) => state.userProfile,
    role: (state) => state.role,
  },
  plugins: [
    createPersistedState({
      paths: ["userId", "userEmail", "userName", "userProfile", "role"],
    }),
  ],
});
