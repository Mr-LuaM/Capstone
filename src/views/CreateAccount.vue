<template>
  <v-container class="fill-height">
    <v-container class="mx-auto">
      <v-card
        class="mx-auto align-self-left pa-9"
        min-width="1000"
        max-width="700"
        border
        rounded
      >
        <v-progress-linear
          :active="loading"
          :indeterminate="loading"
          absolute
          bottom
          color="secondary"
        ></v-progress-linear>
        <v-row>
          <v-col cols="7">
            <div class="mb-6">
              <span class="text-primary font-weight-meduim d-flex align-center">
                <v-img
                  src="../assets/img/logo/pnp-logo.png"
                  alt="Logo"
                  :max-width="20"
                  :max-height="32"
                  aspect-ratio="1/1"
                  cover
                  class="ml-0 pa-0"
                />
                <span class="ml-3 text-h6 font-weight-bold">PnpEduLink</span>
              </span>
            </div>
            <h1 class="text-h5 mb-6">Create you PnpEduLink Account</h1>
            <v-form ref="form" @submit.prevent="submit">
              <v-row>
                <v-col col="6">
                  <FirstNameInput
                    divLabel=""
                    customLabel="FirstName"
                    v-model="FirstName"
                  />
                </v-col>
                <v-col col="6">
                  <LastNameInput
                    divLabel=""
                    customLabel="LastName"
                    v-model="LastName"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col col="12">
                  <EmailInput
                    divLabel=""
                    customLabel="Your Email Address"
                    v-model="Email"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col col="6">
                  <PasswordInput
                    divLabel=""
                    customLabel="Password"
                    v-model="password"
                  />
                </v-col>
                <v-col col="6">
                  <ConfirmPasswordInput
                    v-model="cpassword"
                    divLabel=""
                    :password="password"
                    customHint=""
                    customPersistentHint="false"
                    customPlaceholder="Confirm Password"
                    customLabel="ConfirmPasswordInput"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col col="12">
                  <RolesAutocomplete
                    divLabel=""
                    customLabel="You are....."
                    v-model="Role"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col col="6" class="d-flex">
                  <router-link
                    to="/login"
                    class="d-inline-block text-blue-accent-4 text-decoration-none font-weight-medium text-body-2 mt-3"
                  >
                    Sign In
                  </router-link>

                  <v-spacer></v-spacer>
                  <!-- Your template code -->
                  <v-btn color="primary" variant="flat" @click="signup"
                    >Signup</v-btn
                  >
                </v-col>
              </v-row>
            </v-form>
          </v-col>
          <v-col cols="5" class="d-flex justify-center align-center">
            <v-img
              src="../assets/img/logo/pnp-logo.png"
              alt="Logo"
              :max-width="300"
              :max-height="300"
              aspect-ratio="1/1"
            />
          </v-col>
        </v-row>
      </v-card>
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
  </v-container>
  <infoSnack ref="snackbar" />
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      loading: false,
      FirstName: "", // Placeholder data type: String
      LastName: "", // Placeholder data type: String
      Email: "", // Placeholder data type: String (Email format validation might be useful)
      password: "", // Placeholder data type: String (Considerations for password strength)
      cpassword: null, // Placeholder data type: String
      Role: null, // Placeholder data type: String (Assuming Role is a string, adjust based on actual requirements)
    };
  },
  methods: {
    async signup() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        this.loading = true;
        // Gather form data
        const formData = new FormData();
        formData.append("FirstName", this.FirstName);
        formData.append("LastName", this.LastName);
        formData.append("Email", this.Email);
        formData.append("Password", this.password);
        formData.append("ConfirmPasswordInput", this.cpassword);
        formData.append("Role", this.Role);

        try {
          // Send data to the backend using Axios
          const response = await axios.post("createAccount", formData);

          if (response.data.success === true) {
            this.$refs.snackbar.openSnackbar(response.data.message, "success");
            this.loading = false; // Reset loading state regardless of success or failure
            this.FirstName = null;
            this.LastName = null;
            this.Email = null;
            this.password = null;
            this.cpassword = null;
            this.Role = null;
          } else {
            this.$refs.snackbar.openSnackbar(
              "User Already Registered",
              "error"
            );
            this.loading = false; // Reset loading state regardless of success or failure
          }
        } catch (error) {
          this.$refs.snackbar.openSnackbar("User Already Registered", "error");
          this.loading = false;
        } finally {
        }
      }
    },
    // Other methods as needed
  },
};
</script>
