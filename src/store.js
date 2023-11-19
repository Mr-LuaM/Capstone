// store.js
import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    // Your state variables go here
    token: null,
    user: null,
  },
  mutations: {
    // Your mutations go here
    setToken(state, token) {
      state.token = token;
    },
    setUser(state, user) {
      state.user = user;
    },
  },
  actions: {
    // Your actions go here
    login({ commit }, { token, user }) {
      commit("setToken", token);
      commit("setUser", user);
    },
    logout({ commit }) {
      commit("setToken", null);
      commit("setUser", null);
    },
  },
  getters: {
    // Your getters go here
    isAuthenticated(state) {
      return !!state.token;
    },
  },
});
