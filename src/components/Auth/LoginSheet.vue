<template>
  <v-container>
    <v-sheet
      class="mx-auto align-self-center pa-10"
      min-width="500"
      max-width="500"
      border
      rounded
    >
      <div class="text-center mb-6">
        <span
          class="text-primary font-weight-meduim d-flex align-center justify-center"
        >
          <v-img
            src="../../assets/img/logo/pnp-logo.png"
            alt="Logo"
            :max-width="40"
            :max-height="52"
            aspect-ratio="1/1"
            cover
            class="ml-0 pa-0"
          />
          <span class="ml-3 text-h6 font-weight-bold">PnpEduLink</span>
        </span>
      </div>

      <v-window v-model="step">
        <v-window-item :value="1">
          <div class="text-center">
            <h2 class="mb-4 font-weight-regular">Sign in</h2>
            <span class="d-inline-block mb-8">Use your PnpEduLink Account</span>
          </div>
          <v-form ref="checkEmail" @submit.prevent="checkEmail">
            <EmailInput
              v-model="email"
              divLabel=""
              customDensity="default"
              customVariant="outlined"
              customLabel="Email"
              :showTooltip="false"
              customPlaceholder=""
              :error-messages="emailError"
            />
          </v-form>

          <a
            href="#forgot-email-section"
            class="text-body-2 text-decoration-none text-blue-accent-4 font-weight-medium"
            >Forgot Email?</a
          >

          <div class="text-body-2 text--secondary mt-8 text-grey-lighten-1">
            Not your computer? Use a Private Window to sign in.
          </div>
          <a
            href="#"
            class="d-inline-block text-blue-accent-4 text-decoration-none font-weight-medium text-body-2"
            >Learn More</a
          >
        </v-window-item>

        <v-window-item :value="2">
          <div class="text-center">
            <h2 class="mb-2 font-weight-regular">Welcome</h2>
            <v-chip class="mb-9" variant="outlined">
              <v-avatar left>
                <v-icon> mdi-account-circle </v-icon>
              </v-avatar>
              {{ email }}
              <v-avatar right>
                <v-icon> mdi-chevron-down </v-icon>
              </v-avatar>
            </v-chip>
          </div>
          <v-form ref="form" @submit.prevent="submit">
            <PasswordInput
              divLabel=""
              v-model="password"
              customDensity="default"
              customVariant="outlined"
              customLabel="Password"
              :showTooltip="false"
              customPlaceholder=""
              customHint=""
              :includeUppercase="false"
              :includeLowercase="false"
              :includeDigit="false"
              :includeSpecialChar="false"
              :error-messages="passwordError"
            />
          </v-form>

          <a
            href="#forgot-email-section"
            class="text-body-2 text-decoration-none text-blue-accent-4 font-weight-medium"
            >Forgot Password?</a
          >
        </v-window-item>
      </v-window>

      <v-card-actions class="mt-8 mb-0 pa-0">
        <a
          v-if="step === 1"
          href="#"
          class="d-inline-block text-blue-accent-4 text-decoration-none font-weight-medium text-body-2"
          >Create An Account</a
        >
        <v-btn v-if="step > 1" variant="text" @click="step--"> Back </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          v-if="step === 1"
          color="primary"
          variant="flat"
          @click="checkEmail"
        >
          Next
        </v-btn>
        <v-btn v-if="step === 2" color="primary" variant="flat" @click="submit">
          Login
        </v-btn>
      </v-card-actions>
    </v-sheet>
    <v-row align="center" justify="center">
      <div class="d-flex justify-space-between mt-6">
        <v-btn class="text-none letter-spacing-0" variant="text">
          English (United States)
        </v-btn>
        <div style="margin-right: -12px" class="text-center">
          <v-btn class="text-none letter-spacing-0" variant="text">
            Help
          </v-btn>
          <v-btn class="text-none letter-spacing-0" variant="text">
            Privacy
          </v-btn>
          <v-btn class="text-none letter-spacing-0" variant="text">
            Terms
          </v-btn>
        </div>
      </div>
    </v-row>
  </v-container>
</template>
<script>
import { jwtDecode as jwt_decode } from "jwt-decode";
import axios from "axios";
export default {
  data: () => ({
    step: 1,

    email: "",
    password: "",
    emailError: null,
    passwordError: null,
  }),
  methods: {
    async checkEmail() {
      const { valid } = await this.$refs.checkEmail.validate();

      if (valid) {
        try {
          const formData = new FormData();
          formData.append("Email", this.email);

          // Make the Axios POST request
          const response = await axios.post("checkEmail", formData);

          if (response.data.success === true) {
            this.emailError = null;
            this.step++;
          } else {
            this.emailError = "Email not found";
          }
        } catch (error) {
          console.error(error);
        }
      }
    },
    handleUserRole(role) {
      const router = this.$router; // Access router directly

      if (router) {
        console.log("Role received:", role);

        switch (role) {
          case "1":
            console.log("Redirecting to /admin-dashboard");
            router.push("/admin/applicants");
            break;
          case "6":
            console.log("Redirecting to /applicant-dashboard");
            router.push("/applicant-dashboard");
            break;
          case "2":
            console.log("Redirecting to /main-admin-dashboard");
            router.push("/main-admin-dashboard");
            break;
          case "3":
            console.log("Redirecting to /station-admin-dashboard");
            router.push("/station-admin-dashboard");
            break;
          case "5":
            console.log("Redirecting to /student-dashboard");
            router.push("/admin/applicants");
            break;
          case "4":
            console.log("Redirecting to /teacher-dashboard");
            router.push("/teacher-dashboard");
            break;
          default:
            console.log("Redirecting to /hi");
            router.push("/hi");
        }
      }
    },

    async submit() {
      const { valid } = await this.$refs.form.validate();

      if (valid) {
        try {
          const formData = new FormData();
          formData.append("Email", this.email);
          formData.append("Password", this.password);

          const response = await axios.post("login", formData);

          if (response.data.token) {
            const token = response.data.token;

            localStorage.setItem("jwt_token", token);

            // Use jwt_decode alias to decode the token
            const decodedToken = jwt_decode(token);
            console.log(decodedToken);

            this.handleUserRole(decodedToken.role);
          } else {
            this.passwordError = "error";
          }
        } catch (error) {
          this.passwordError = "Wrong Password";
          console.error(error);
        }
      }
    },
  },
};
</script>
