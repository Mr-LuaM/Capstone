<template>
  <v-app>
    <v-main>
      <v-container fluid fill-height>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-2 rounded-l">
              <v-card-title class="text-center">
                <v-img
                  src="@/assets/img/logo/pnp-logo.png"
                  alt="Logo"
                  max-height="100"
                ></v-img>
                <div>Welcome</div>
              </v-card-title>

              <v-card-text>
                <v-form id="formAuthentication" @submit.prevent="signIn">
                  <v-text-field
                    v-model="email"
                    label="Email or Username"
                    outlined
                    autofocus
                  ></v-text-field>

                  <v-text-field
                    v-model="password"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showPassword ? 'text' : 'password'"
                    label="Password"
                    outlined
                    @click:append="showPassword = !showPassword"
                  ></v-text-field>

                  <v-checkbox
                    v-model="rememberMe"
                    label="Remember Me"
                  ></v-checkbox>

                  <v-btn color="primary" block @click="signIn" class="large-btn"
                    >Sign in</v-btn
                  >
                </v-form>
                <div class="text-center mt-4">
                  <v-btn
                    color="danger"
                    text
                    @click="forgotPassword"
                    class="text-left"
                    >Forgot Password</v-btn
                  >
                  <v-divider vertical></v-divider>
                  <v-btn text @click="contactUs" class="text-right"
                    >Contact Us</v-btn
                  >
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      email: "",
      password: "",
      rememberMe: false,
      showPassword: false,
    };
  },
  methods: {
    signIn() {
      // Add your sign-in logic here
      console.log("Sign in clicked");
    },
    register() {
      // Add your registration logic here
      console.log("Register clicked");
    },
    socialSignIn(provider) {
      // Add your social sign-in logic here
      console.log(`Sign in with ${provider}`);
    },
  },
};
</script>

<style scoped>
.large-btn {
  font-size: 1.25rem;
}
.text-left {
  text-align: left;
}
.text-right {
  text-align: right;
}
</style>
