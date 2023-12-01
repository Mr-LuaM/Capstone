<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card>
      <v-toolbar color="primary-darken-1">
        <v-app-bar-nav-icon>
          <v-icon icon="mdi-account"></v-icon
        ></v-app-bar-nav-icon>
        &nbsp;
        <v-toolbar-title>Manage Accounts and Roles</v-toolbar-title>

        <v-spacer></v-spacer>

        <template v-slot:extension>
          <v-tabs v-model="tab" centered bg-color="primary" grow>
            <v-tab value="StationAccounts">Station Accounts</v-tab>
            <v-tab value="TeacherAccounts">Teachers</v-tab>
          </v-tabs>
        </template>
      </v-toolbar>

      <v-window v-model="tab">
        <v-window-item value="StationAccounts" class="pa-8">
          <v-card flat>
            <v-card-title class="d-flex align-center pe-2">
              Station Admin Accounts

              <v-spacer></v-spacer>

              <v-col cols="auto">
                <v-btn
                  icon="mdi-filter-variant"
                  size="small"
                  id="menu-activator"
                  color="black"
                  variant="text"
                ></v-btn>
              </v-col>

              <v-menu
                activator="#menu-activator"
                :close-on-content-click="false"
              >
                <v-list>
                  <v-container fluid>
                    <v-checkbox
                      v-model="filter"
                      label="active"
                      value="active"
                      class="px-0 mx-0"
                      hide-details
                    ></v-checkbox>
                    <v-checkbox
                      v-model="filter"
                      label="inactive"
                      value="inactive"
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
              :headers="headers"
              :items="filteredStationAdmins"
              :item-value="(item) => `${item.name}-${item.StationAdmin_ID}`"
              class="elevation-1 rounded-lg"
              show-select
              v-model="selected"
              return-object
              hover="primary"
            >
              <template v-slot:item.Profile_Picture="{ value }">
                <v-avatar
                  :image="getFullImageUrl(value)"
                  size="45"
                  class="elevation-8"
                ></v-avatar>
              </template>
              <template v-slot:item.Full_Name="{ value }">
                {{ value }}
              </template>

              <template v-slot:item.Status="{ value }">
                <v-chip :color="getColor(value)" size="small" label>
                  {{ value }}
                </v-chip>
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn
                  density="compact"
                  size="x-large"
                  @click="showDetails(item)"
                  color="secondary"
                  variant="plain"
                  icon="mdi-eye"
                >
                </v-btn>

                <v-btn
                  density="compact"
                  size="x-large"
                  icon="mdi-pencil"
                  @click="editStationAdmin(item)"
                  color="secondary"
                  variant="plain"
                >
                </v-btn>

                <v-btn
                  size="x-large"
                  @click="openConfirmDialog(item.StationAdmin_ID)"
                  :color="item.Status === 'inactive' ? 'success' : 'error'"
                  density="compact"
                  icon="mdi-swap-horizontal-circle-outline"
                  variant="plain"
                >
                </v-btn>
              </template>
            </v-data-table>
          </v-card>
        </v-window-item>
        <v-window-item value="TeacherAccounts">
          <v-card>
            <v-card-text>ss</v-card-text>
          </v-card>
        </v-window-item>
      </v-window>
    </v-card>

    <v-dialog v-model="dialog" persistent width="800">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ formTitle }}</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" @submit.prevent="submit">
              <v-row justify="center" align="center">
                <!-- Add your form fields and logic here -->
                <!-- For example: -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="editedStationAdmin.name"
                    label="Name"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="secondary" variant="plain" @click="closeDialog">
            Close
          </v-btn>
          <v-btn
            v-if="isEditing"
            variant="text"
            @click="saveEditedStationAdmin"
            color="primary"
          >
            Save Changes
          </v-btn>
          <v-btn v-else variant="text" @click="addStationAdmin" color="primary">
            Add Station Admin
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>

  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>
<script>
import { getStationAdminsWithStation } from "@/services/BackendApi"; // Assuming you have an API module or file
import axios from "axios";

export default {
  data() {
    return {
      tab: null,
      StationAdmins: [],
      loading: false, // Add loading state
      selectedStationAdmins: {},
      originalSelectedStationAdmins: {},
      filter: ["active"],
      search: "",

      headers: [
        {
          title: "Actions",
          value: "actions",
          sortable: false,
          width: "170px",
          align: "end",
        },
        {
          title: "Profile Picture",
          value: "Profile_Picture",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Full Name",
          value: "Full_Name",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Age",
          value: "Age",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Sex",
          value: "Sex",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Address",
          value: "Address",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Birthday",
          value: "Birthday",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Birthplace",
          value: "Birthplace",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Status",
          value: "Status",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Nationality",
          value: "Nationality",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Religion",
          value: "Religion",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Admin Phone Number",
          value: "Admin_PhoneNum",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Station Name",
          value: "Station_Name",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Location",
          value: "Location",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Station Status",
          value: "status",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Created At",
          value: "created_at",
          align: "start",
          width: "150px",
          sortable: true,
        },
        {
          title: "Last Updated At",
          value: "status_updated_at",
          align: "start",
          width: "150px",
          sortable: true,
        },
      ],
      dialog: false,
      editedStationAdmin: {},
      originalEditedStationAdmin: {},
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    async loadData() {
      this.loading = true;

      try {
        this.StationAdmins = await getStationAdminsWithStation();
      } catch (error) {
        console.error("Failed to fetch station admins:", error);
      } finally {
        this.loading = false;
      }
    },
    getColor(status) {
      return status === "active" ? "success" : "primary";
    },
    getFullImageUrl(imagePath) {
      // Replace with the actual base URL of your server
      const baseUrl = "http://backend.test/";

      // Combine the base URL and the image path to get the full image URL
      return baseUrl + imagePath;
    },
    showDetails(item) {
      // Find the applicant with the selected ID
      this.originalselectedStationAdmins = item;
      console.log(this.originalselectedStationAdmins);
      this.dialog = true;
    },
    openConfirmDialog(id) {
      // Open the confirm dialog for approving or rejecting an application
      this.$refs.confirmationModal.dialog = true;
      this.$refs.confirmationModal.confirmAction = () => this.toggleStatus(id);
    },
    editStationAdmin(stationAdmin) {
      this.dialog = true;
      this.editedStationAdmin = { ...stationAdmin };
      this.originalEditedStationAdmin = { ...stationAdmin };
    },
    async toggleStatus(id) {
      try {
        const secureTokenResponse = await axios.get(
          "generateSecureToken/" + id
        );
        const secureToken = secureTokenResponse.data;
        // Use axios or your preferred HTTP library to send a request to your backend
        const response = await axios.post("toggleStationAdminStatus/" + id, {
          secureToken,
        });
        if (response.data.success === true) {
          // Show a success alert or perform other success-related actions here
          this.$refs.snackbar.openSnackbar(response.data.message, "success");
          this.$refs.confirmationModal.dialog = false;
          this.loadData();
        } else {
          // Show the error Snackbar
          this.$refs.snackbar.openSnackbar(response.data.message, "error");
        }
      } catch (error) {
        console.error("Error inactivating station:", error);
        // Handle errors as needed
      }
    },
  },
  computed: {
    filteredStationAdmins() {
      return this.StationAdmins.map((stationAdmin) => {
        const fullName =
          `${stationAdmin.First_Name} ${stationAdmin.Middle_Name} ${stationAdmin.Last_Name} ${stationAdmin.Name_Extension}`.trim();
        return { ...stationAdmin, Full_Name: fullName };
      }).filter((stationAdmin) => {
        const statusFilter =
          this.filter.length === 0 || this.filter.includes(stationAdmin.Status);

        const searchFilter =
          !this.search ||
          Object.values(stationAdmin).some(
            (value) =>
              typeof value === "string" &&
              value.toLowerCase().includes(this.search.toLowerCase())
          );

        return statusFilter && searchFilter;
      });
    },
    formTitle() {
      return this.isEditing ? "Edit Course" : "Add Course";
    },
    isEditing() {
      return (
        Boolean(this.editedStationAdmin.StationAdmin_ID) ||
        this.editedStationAdmin.StationAdmin_ID === 0
      );
    },
  },
};
</script>
