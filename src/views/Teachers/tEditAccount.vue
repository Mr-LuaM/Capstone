<template>
  <v-col class="mb-6">
    <v-card max-width="100%" class="bg-background rounded" elevation="2">
      <v-img
        height="200px"
        src="../../assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
        cover
      ></v-img>

      <v-row justify="center">
        <v-col
          align-self="start"
          class="d-flex justify-left align-left pa-0 mt-n16 ml-16"
          cols="12"
        >
          <v-avatar
            class="profile avatar-center-height avatar-shadow"
            color="grey"
            size="164"
          >
            <v-img :src="imageSource" cover></v-img>
          </v-avatar>
        </v-col>
        <v-col class="text-right mt-n16 ml-n16" cols="12">
          <v-btn
            :color="isEditing ? 'success' : 'secondary'"
            @click="isEditing ? openConfirmDialog() : editClick()"
            class="mr-2"
          >
            <v-icon>{{ isEditing ? "mdi-check" : "mdi-pencil" }}</v-icon>
          </v-btn>

          <v-btn v-if="isEditing" color="error" @click="cancel">
            <v-icon>mdi-cancel</v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <v-list-item color="#0000" class="profile-text-name ma-4">
        <v-list-item-content>
          <v-form ref="form" @submit.prevent="submit">
            <v-row class="mt-4">
              <v-col col="12" md="12">
                <ImageInput
                  :readonly="!isEditing"
                  v-model="Profile_Picture"
                  @change="handleFileChange"
                />
              </v-col>
              <v-col col="12" md="3">
                <LastNameInput
                  divLabel=""
                  customLabel="Last Name"
                  :readonly="!isEditing"
                  v-model="UserDetails.Last_Name"
                />
              </v-col>
              <v-col col="12" md="3">
                <FirstNameInput
                  divLabel=""
                  customLabel="First Name"
                  :readonly="!isEditing"
                  v-model="UserDetails.First_Name"
                />
              </v-col>
              <v-col col="12" md="3">
                <MiddleNameInput
                  divLabel=""
                  customLabel="Middle Name"
                  :readonly="!isEditing"
                  v-model="UserDetails.Middle_Name"
                />
              </v-col>
              <v-col col="12" md="3">
                <NameExtensionInput
                  divLabel=""
                  customLabel="Name Extension"
                  :readonly="!isEditing"
                  v-model="UserDetails.Name_Extension"
                />
              </v-col>
              <v-col col="12" md="1">
                <GenericNumber
                  divLabel=""
                  customLabel="Age"
                  :readonly="!isEditing"
                  v-model="UserDetails.Age"
                />
              </v-col>

              <v-col cols="6" sm="2">
                <RadioButtonsInput
                  v-model="UserDetails.Sex"
                  labelText=""
                  :options="['Male', 'Female']"
                  :inline="true"
                  :readonly="!isEditing"
                />
              </v-col>
              <v-col col="12" md="3">
                <Address
                  divLabel=""
                  customLabel="Address"
                  :readonly="!isEditing"
                  v-model="UserDetails.Address"
                />
              </v-col>
              <v-col col="12" md="3">
                <BDateInput
                  divLabel=""
                  labelText="Birthday"
                  :readonly="!isEditing"
                  v-model="UserDetails.Birthday"
                />
              </v-col>
              <v-col col="12" md="3">
                <NationalityInput
                  divLabel=""
                  customLabel="Nationality"
                  :readonly="!isEditing"
                  v-model="UserDetails.Nationality"
                />
              </v-col>
              <v-col col="12" md="3">
                <ReligionInput
                  divLabel=""
                  customLabel="Religion"
                  :readonly="!isEditing"
                  v-model="UserDetails.Religion"
                />
              </v-col>
              <v-col col="12" md="3">
                <PhoneNumberInput
                  divLabel=""
                  customLabel="Phone Number"
                  :readonly="!isEditing"
                  v-model="UserDetails.Teacher_PhoneNum"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-list-item-content>
      </v-list-item>
    </v-card>
  </v-col>
  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      isEditing: false,
      UserDetails: [],
      selectedFile: null,
      originalUserDetails: {},
    };
  },
  created() {
    this.loadData();
  },
  computed: {
    imageSource() {
      // If a file is selected, show the preview
      if (this.selectedFile) {
        return URL.createObjectURL(this.selectedFile);
      } else {
        // If no file is selected, show the backend image
        return "http://backend.test/" + this.UserDetails.Profile_Picture;
      }
    },
  },

  methods: {
    handleFileChange(event) {
      this.selectedFile = event.target.files[0];
    },
    async loadData() {
      try {
        const userId = this.$store.state.userId;
        const userRole = this.$store.state.role;
        const secureTokenResponse = await axios.get(
          "generateSecureToken/" + userId
        );
        const secureToken = secureTokenResponse.data;

        // Call the getAdminEditDetails function from the service with the correct parameter names
        const UserDetails = await axios.post("getTeacherEditDetails", {
          userId,
          userRole,
          secureToken,
        });

        this.UserDetails = UserDetails.data;
      } catch (error) {
        // Log the error using console.error
        console.error("Failed to fetch user details:", error);
      }
    },

    editClick() {
      // Handle edit click
      this.isEditing = true;
      this.originalUserDetails = { ...this.UserDetails };
    },
    async openConfirmDialog() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        // Open the confirm dialog for approving or rejecting an application
        this.$refs.confirmationModal.dialog = true;
        this.$refs.confirmationModal.confirmAction = () => this.save();
      } else {
        this.$refs.snackbar.openSnackbar("Validation error", "error");
      }
    },
    async save() {
      try {
        const formData = new FormData();

        // Append the updated user details to the FormData
        formData.append("userId", this.UserDetails.Teacher_ID);
        formData.append("userRole", this.$store.state.role);
        formData.append("secureToken", this.secureToken);

        // Append other updated fields as needed
        formData.append("firstName", this.UserDetails.First_Name);
        formData.append("lastName", this.UserDetails.Last_Name);
        formData.append("middleName", this.UserDetails.Middle_Name);
        formData.append("nameExtension", this.UserDetails.Name_Extension);
        formData.append("age", this.UserDetails.Age);
        formData.append("sex", this.UserDetails.Sex);
        formData.append("address", this.UserDetails.Address);
        formData.append("birthday", this.UserDetails.Birthday);
        formData.append("nationality", this.UserDetails.Nationality);
        formData.append("religion", this.UserDetails.Religion);
        formData.append("phoneNumber", this.UserDetails.Teacher_PhoneNum);

        // Check if a new profile picture file is selected
        if (this.selectedFile) {
          formData.append("profilePicture", this.selectedFile);
        }

        // ... (append other fields as needed)

        const response = await axios.post("updateTeacherDetails", formData);

        if (response.data.success) {
          this.$refs.snackbar.openSnackbar(response.data.message, "success");
          this.$refs.confirmationModal.dialog = false;
          this.loadData();
          await this.$store.dispatch("fetchUserDetails");
        } else {
          this.$refs.snackbar.openSnackbar(response.data.message, "error");
          // Log or display a more detailed error message
          console.error("Failed to update user details:", response.data.error);
          this.UserDetails = { ...this.originalUserDetails };
        }
      } catch (error) {
        this.$refs.snackbar.openSnackbar("ERROR", "error");
        this.$refs.confirmationModal.dialog = false;
        this.UserDetails = { ...this.originalUserDetails };
        this.selectedFile = null;
      }

      // Exit editing mode
      this.isEditing = false;
    },

    cancel() {
      // Handle cancel click
      this.isEditing = false;
      this.UserDetails = { ...this.originalUserDetails };
      this.selectedFile = null;
    },
  },
};
</script>
<style>
.gradient-cover {
  position: relative;
}

.gradient-cover::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: linear-gradient(
    to right,
    hsla(210, 100%, 50%, 1) 0%,
    hsla(0, 100%, 50%, 1) 35%,
    hsla(0, 100%, 50%, 1) 74%
  );
  opacity: 0.7; /* Adjust the opacity as needed */
}

.upload-btn {
  position: absolute !important;
  z-index: 999;
  top: 87%;
  left: 65%;
  transform: translate(-50%, -50%);
  color: #fff; /* White text for better visibility */
  background-color: rgba(0, 0, 0, 0.897); /* Green color for the button */
  padding: 10px 20px;
  border: none;
  border-radius: 55px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Subtle box shadow */
}

.avatar-shadow {
  outline-color: white;
  box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75); /* Adjust the color to PNP blue */
  -webkit-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
  -moz-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
}
</style>
