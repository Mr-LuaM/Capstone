<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-secondary-darken-1">
        <v-icon icon="mdi-account"></v-icon> &nbsp; Review Applicant

        <v-spacer></v-spacer>

        <v-col cols="auto">
          <v-btn
            icon="mdi-filter-variant"
            size="small"
            id="menu-activator"
            color="white"
            variant="text"
          ></v-btn>
        </v-col>

        <v-menu activator="#menu-activator" :close-on-content-click="false">
          <v-list>
            <v-container fluid>
          
              <v-checkbox
                v-model="filter"
                label="Pending"
                value="pending"
                class="px-0 mx-0"
                hide-details
              ></v-checkbox>
              <v-checkbox
                v-model="filter"
                label="Approved"
                value="approved"
                class="px-0 mx-0"
                hide-details
              ></v-checkbox>
              <v-checkbox
                v-model="filter"
                label="Rejected"
                value="rejected"
                class="px-0 mx-0"
                hide-details
              ></v-checkbox>
            </v-container>
          </v-list>
        </v-menu>
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          density="compact"
          label="Search"
          single-line
          flat
          hide-details
          variant="outlined"
        ></v-text-field>
      </v-card-title>

      <v-divider></v-divider>
      <v-data-table
        v-model:search="search"
        width="100%"
        show-select
        v-model="selected"
        :headers="headers"
        :items="filteredApplicants"
        :item-value="(item) => `${item.name}-${item.id}`"
        class="elevation-1"
        return-object

      >
        <template v-slot:item.Selected_File1="{ value }">
          <v-card class="my-2" elevation="2" rounded>
            <v-img height="64" cover :src="getFullImageUrl(value)"></v-img>
          </v-card>
        </template>
        <template v-slot:item.Status="{ value }">
          <v-chip :color="getColor(value)" size="small" label>
            {{ value }}
          </v-chip>
        </template>
        <template v-slot:item.Email="{ value }">
          <v-chip :color="getColor(value)" size="small" label>
            {{ value }}
          </v-chip>
        </template>
        <template v-slot:item.Course1="{ value }">
          <div class="text-end">
            <v-chip :color="getColor(value)" size="small" label>
              {{ value }}
            </v-chip>
          </div>
        </template>
        <template v-slot:item.Station1="{ value }">
          <div class="text-end">
            <v-chip :color="getColor(value)" size="small" label>
              {{ value }}
            </v-chip>
          </div>
        </template>
        <template v-slot:item.actions="{ item }" >
          <v-btn  density="compact"  size="x-large" @click="showDetails(item)" color="secondary" variant="plain" icon="mdi-eye">
       
          </v-btn>

          <v-btn  density="compact"  size="x-large" @click="quickapprove(item)" color="success" variant="plain" icon="mdi-content-save" >
           
          </v-btn>

          <v-btn
             density="compact"  size="x-large"
            @click="openConfirmDialog(item.id)"
            color="primary"
            variant="plain"
            icon="mdi-archive"
           
          >
          
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
  <v-dialog v-model="dialog" width="80%">
    <v-card>
      <v-card-title class="green darken-1 white--text">
        <span class="text-h5">Student Information</span>
      </v-card-title>
      <v-card-text>
        <FormSheet
          :last-name="formattedSelectedApplicant.lastName"
          :first-name="formattedSelectedApplicant.firstName"
          :middle-name="formattedSelectedApplicant.middleName"
          :name-extension="formattedSelectedApplicant.nameExtension"
          :sex="formattedSelectedApplicant.sex"
          :Bdate="formattedSelectedApplicant.Bdate"
          :nationality="formattedSelectedApplicant.nationality"
          :religion="formattedSelectedApplicant.religion"
          :height="formattedSelectedApplicant.height"
          :weight="formattedSelectedApplicant.weight"
          :address="formattedSelectedApplicant.address"
          :email="formattedSelectedApplicant.email"
          :phone-number="formattedSelectedApplicant.phoneNumber"
          :course1="formattedSelectedApplicant.course1"
          :station1="formattedSelectedApplicant.station1"
          :course2="formattedSelectedApplicant.course2"
          :station2="formattedSelectedApplicant.station2"
          :course3="formattedSelectedApplicant.course3"
          :station3="formattedSelectedApplicant.station3"
          :selectedFile1="
            getFullImageUrl(formattedSelectedApplicant.selectedFile1)
          "
        />
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="red darken-1"
          text
          @click="openConfirmDialog(selectedApplicant.id)"
        >
          Archive
        </v-btn>
        <v-btn color="green darken-1" text @click="">
          Approve
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog2" max-width="800">
    <v-card>
      <v-card-title class="bg-secondary">
        <span class="text-h5">Choose Course and Station</span>
      </v-card-title>
      <v-card-text class="pa-4">
        <v-row>
          <v-col cols="4" md="2">
            <v-checkbox-btn
              v-model="enabled"
              class="pe-2"
              @change="updateSelection(1)"
            ></v-checkbox-btn>
          </v-col>
          <v-col cols="4" md="5">
            <!-- Course 1 -->
            <div>Course</div>
            <v-text-field
              :disabled="!enabled"
              :value="formattedSelectedApplicant.course1"
              readonly
              variant="outlined"
              density="dense"
              hide-details="auto"
            ></v-text-field>
          </v-col>
          <v-col cols="4" md="5">
            <!-- Station 1 -->
            <div>Station</div>
            <v-text-field
              :disabled="!enabled"
              :value="formattedSelectedApplicant.station1"
              readonly
              variant="outlined"
              density="dense"
              hide-details="auto"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="4" md="2">
            <v-checkbox-btn
              v-model="enabled2"
              class="pe-2"
              @change="updateSelection(2)"
            ></v-checkbox-btn>
          </v-col>
          <v-col cols="4" md="5">
            <!-- Course 1 -->
            <div>Course</div>
            <v-text-field
              :disabled="!enabled2"
              :value="formattedSelectedApplicant.course2"
              readonly
              variant="outlined"
              density="dense"
              hide-details="auto"
            ></v-text-field>
          </v-col>
          <v-col cols="4" md="5">
            <!-- Station 1 -->
            <div>Station</div>
            <v-text-field
              :disabled="!enabled2"
              :value="formattedSelectedApplicant.station2"
              readonly
              variant="outlined"
              density="dense"
              hide-details="auto"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="4" md="2">
            <v-checkbox-btn
              v-model="enabled3"
              class="pe-2"
              @change="updateSelection(3)"
            ></v-checkbox-btn>
          </v-col>
          <v-col cols="4" md="5">
            <!-- Course 1 -->
            <div>Course</div>
            <v-text-field
              :disabled="!enabled3"
              :value="formattedSelectedApplicant.course3"
              readonly
              variant="outlined"
              density="dense"
              hide-details="auto"
            ></v-text-field>
          </v-col>
          <v-col cols="4" md="5">
            <!-- Station 1 -->
            <div>Station</div>
            <v-text-field
              :disabled="!enabled3"
              :value="formattedSelectedApplicant.station3"
              readonly
              variant="outlined"
              density="dense"
              hide-details="auto"
            ></v-text-field>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="green darken-1" @click="submitApplication">
          Approve
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="cancelArchive"
  />
</template>

<!-- <v-img width="500" src="../../../../../../backend/selectedFile1"></v-img> -->
<script>
import axios from "axios";
import { getApplicants } from "../../../services/BackendApi.js";
import FormSheet from "../../../components/Application/FormSheet";
import EmailInputModal from "../../../components/dialogs/emailInputModal";

export default {
  components: {
    FormSheet,
    EmailInputModal,
  },
  data() {
    return {
      selectedApplicant: null,
      filter: ["pending"],
      approvedCourse: "",
      approvedStation: "",

      enabled: false,
      enabled2: false,
      enabled3: false,
      search: "",
      dialog2: false,
      email: "",
      password: "",

      itemsPerPage: 5,
      selected: null,
      selectedId: null,
      dialog: false,
      headers: [
        {
          title: "Actions",
          key: "actions",
          sortable: false,
          width: "190px",
        },
        { title: "Status", key: "Status" },
        { title: "IMG", key: "Selected_File1" },
        { title: "ID", key: "id" },
        { title: "Last Name", key: "Last_Name", width: "140px" },
        { title: "First Name", key: "First_Name", width: "140px" },
        { title: "Middle Name", key: "Middle_Name" },
        { title: "Name Extension", key: "Name_Extension" },
        { title: "Sex", key: "Sex" },
        { title: "Bdate", key: "Bdate" },
        { title: "Age", key: "Age" },
        { title: "Nationality", key: "Nationality" },
        { title: "Religion", key: "Religion" },
        { title: "Height", key: "Height" },
        { title: "Weight", key: "Weight" },
        { title: "Address", key: "Address" },
        { title: "Email", key: "Email" },
        { title: "Phone Number", key: "Phone_Number", width: "140px" },

        { title: "Course1", key: "Course1" },
        { title: "Station1", key: "Station1" },
        { title: "Course2", key: "Course2" },
        { title: "Station2", key: "Station2" },
        { title: "Course3", key: "Course3" },
        { title: "Station3", key: "Station3" },
        { title: "Date Of Application", key: "Date_Of_Application" },
      ],
      applicants: [],
      loading: false, // Loading state
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    // Your other methods go here
    async loadData() {
      this.loading = true; // Set loading state to true

      try {
        // Fetch applicants data from the backend
        this.applicants = await getApplicants();
      } catch (error) {
        console.error("Failed to fetch applicants:", error);
      } finally {
        this.loading = false; // Set loading state to false when data is loaded or request fails
      }
    },
    async sendEmail() {
      try {
        await this.submitApplication(); // Wait for the email sending to complete
        this.closeDialog();
      } catch (error) {
        // Handle any errors that occur during the email sending process
        console.error(error);
        // You might want to show an error message to the user
      }
    },
    closeDialog() {
      this.dialog = false;
      this.dialog2 = false;
    },
    async submitApplication() {
      if (this.enabled || this.enabled2 || this.enabled3) {
        this.closeDialog();
        try {
          const formData = new FormData();

          // formData.append("sendingemail", this.selectedApplicant.email);
          formData.append("id", this.selectedApplicant.id);
          // formData.append("password", this.pasword);
          formData.append("Email", this.selectedApplicant.Email);
          formData.append("Last_Name", this.selectedApplicant.Last_Name);
          formData.append("First_Name", this.selectedApplicant.First_Name);
          formData.append("Middle_Name", this.selectedApplicant.Middle_Name);
          formData.append(
            "Name_Extension",
            this.selectedApplicant.Name_Extension
          );
          formData.append("Sex", this.selectedApplicant.Sex);
          formData.append("Bdate", this.selectedApplicant.Bdate);
          formData.append("Age", this.selectedApplicant.Age);
          formData.append("Address", this.selectedApplicant.Address);
          formData.append("Nationality", this.selectedApplicant.Nationality);
          formData.append("Religion", this.selectedApplicant.Religion);
          formData.append("Phone_Number", this.selectedApplicant.Phone_Number);
          formData.append("Profile", this.selectedApplicant.Selected_File1);
          formData.append("approvedCourse", this.approvedCourse);
          formData.append("approvedStation", this.approvedStation);
          formData.append("Status", this.selectedApplicant.Status);
          // console.log("Last_Name:", this.selectedApplicant.Last_Name);
          // console.log("First_Name:", this.selectedApplicant.First_Name);
          // console.log("Middle_Name:", this.selectedApplicant.Middle_Name);
          // console.log("Name_Extension:", this.selectedApplicant.Name_Extension);
          // console.log("Sex:", this.selectedApplicant.Sex);
          // console.log("Bdate:", this.selectedApplicant.Bdate);
          // console.log("Age:", this.selectedApplicant.Age);
          // console.log("Address:", this.selectedApplicant.Address);
          // console.log("Nationality:", this.selectedApplicant.Nationality);
          // console.log("Religion:", this.selectedApplicant.Religion);
          // console.log("Phone_Number:", this.selectedApplicant.Phone_Number);
          // console.log("Profile:", this.selectedApplicant.Selected_File1);
          // console.log("approvedCourse:", this.approvedCourse);
          // console.log("approvedStation:", this.approvedStation);

          // Make the Axios POST request
          const respond = await axios.post("approve", formData);
          if (respond.data === true) {
            // Show a success alert or perform other success-related actions here
            this.$refs.snackbar.openSnackbar("Success message!", "success");
            this.loadData();
          } else {
            // Show the error Snackbar
            this.$refs.snackbar.openSnackbar("Error message!", "error");
          }
        } catch (error) {
          console.log(error);
          this.$refs.snackbar.openSnackbar("Error message11!", "error");
        }
      }
    },
    getFullImageUrl(imagePath) {
      // Replace with the actual base URL of your server
      const baseUrl = "http://backend.test/";

      // Combine the base URL and the image path to get the full image URL
      return baseUrl + imagePath;
    },
    getColor(status) {
      if (status === "pending") return "warning";
      else if (status === "approved") return "success";
      else if (status === "rejected") return "error";
      else return "primary";
    },
    showDetails(item) {
      // Find the applicant with the selected ID
      this.selectedApplicant = item;
      console.log(this.selectedApplicant);
      this.dialog = true;
    },
    quickapprove(item) {
      // Find the applicant with the selected ID
      this.selectedApplicant = item;
      console.log(this.selectedApplicant);
      this.dialog2 = true;
    },
    openEmailModal(item) {
      // Open the email input modal
      this.$refs.emailInputModal.dialog = true;
    },
    updateSelection(choice) {
      // Disable other checkboxes based on the selected choice
      this.enabled = choice === 1;
      this.enabled2 = choice === 2;
      this.enabled3 = choice === 3;

      // Set approvedCourse and approvedStation based on the selected choice
      switch (choice) {
        case 1:
          this.approvedCourse = this.formattedSelectedApplicant.course1;
          this.approvedStation = this.formattedSelectedApplicant.station1;
          break;
        case 2:
          this.approvedCourse = this.formattedSelectedApplicant.course2;
          this.approvedStation = this.formattedSelectedApplicant.station2;
          break;
        case 3:
          this.approvedCourse = this.formattedSelectedApplicant.course3;
          this.approvedStation = this.formattedSelectedApplicant.station3;
          break;
        default:
          // Handle other cases if needed
          break;
      }
    },
    openConfirmDialog(id) {
      // Open the confirm dialog for approving or rejecting an application
      this.$refs.confirmationModal.dialog = true;
      this.$refs.confirmationModal.confirmAction = () => this.archiveItem(id);
    },

    async archiveItem(id) {
      console.log(id);

      try {
        // Fetch the secure token from the server
        const secureTokenResponse = await axios.get(
          "generateSecureToken/" + id
        );
        const secureToken = secureTokenResponse.data;

        // Make the Axios POST request with both ID and secure token
        const response = await axios.post("reject/" + id, { secureToken });

        if (response.data === "Applicant rejected successfully.") {
          // Show a success alert or perform other success-related actions here
          this.$refs.snackbar.openSnackbar("Success message!", "success");
          this.loadData();
        } else {
          // Show the error Snackbar
          this.$refs.snackbar.openSnackbar("Error message!", "error");
        }
      } catch (error) {
        console.error(error);
        this.$refs.snackbar.openSnackbar("Error message!", "error");
      }

      this.$refs.confirmationModal.dialog = false;
    },

    cancelArchive() {
      // Reset the selected item and close the confirmation dialog
      this.selectedApplicant = null;
      this.$refs.confirmationModal.dialog = false;
    },
  },
  computed: {
    filteredApplicants() {
      // Filter applicants based on the selected status checkboxes and search input
      return this.applicants.filter((applicant) => {
        const statusFilter =
          this.filter.length === 0 || this.filter.includes(applicant.Status);

        const searchFilter =
          !this.search ||
          Object.values(applicant).some(
            (value) =>
              typeof value === "string" &&
              value.toLowerCase().includes(this.search.toLowerCase())
          );

        return statusFilter && searchFilter;
      });
    },
    formattedSelectedApplicant() {
      console.log("ss");
      if (this.selectedApplicant) {
        // Create an object with the selected applicant's details
        return {
          id:this.selectedApplicant.id,
          lastName: this.selectedApplicant.Last_Name,
          firstName: this.selectedApplicant.First_Name,
          middleName: this.selectedApplicant.Middle_Name,
          nameExtension: this.selectedApplicant.Name_Extension,
          sex: this.selectedApplicant.Sex,
          Bdate: this.selectedApplicant.Bdate,
          age: this.selectedApplicant.Age,
          nationality: this.selectedApplicant.Nationality,
          religion: this.selectedApplicant.Religion,
          height: this.selectedApplicant.Height,
          weight: this.selectedApplicant.Weight,
          address: this.selectedApplicant.Address,
          email: this.selectedApplicant.Email,
          phoneNumber: this.selectedApplicant.Phone_Number,
          selectedFile1: this.selectedApplicant.Selected_File1,
          course1: this.selectedApplicant.Course1,
          station1: this.selectedApplicant.Station1,
          course2: this.selectedApplicant.Course2,
          station2: this.selectedApplicant.Station2,
          course3: this.selectedApplicant.Course3,
          station3: this.selectedApplicant.Station3,
          // Add other details here
        };
      }
      return {
        id: 1,
        lastName: "lastName",
        firstName: "firstName",
        middleName: "middleName",
        nameExtension: "nameExtension",
        sex: "sex",
        Bdate: "Bdate",
        age: "age",
        nationality: "nationality",
        religion: "religion",
        height: "height",
        weight: "weight",
        address: "address",
        email: "email",
        phoneNumber: "phoneNumber",
        selectedFile1: "selectedFile1",
        course1: "course1",
        station1: "station1",
        course2: "course2",
        station2: "station2",
        course3: "course3",
        station3: "station3",
      }; // Return null if no selected applicant
    },
  },
};
</script>
