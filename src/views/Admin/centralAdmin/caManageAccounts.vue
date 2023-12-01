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
          <v-data-iterator
            :items="StationAdmins"
            :items-per-page="itemsPerPage"
            :search="search"
            :item-value="(item) => item.StationAdmin_ID"
          >
            <template v-slot:header="{ page, pageCount, prevPage, nextPage }">
              <h4
                class="text-h6 font-weight-bold d-flex justify-space-between mb-4 align-center"
              >
                <div class="text-truncate">Station Admins</div>

                <div class="d-flex align-center">
                  <v-text-field
                    v-model="search"
                    clearable
                    density="comfortable"
                    hide-details
                    placeholder="Search"
                    prepend-inner-icon="mdi-magnify"
                    style="width: 300px"
                    variant="outlined"
                  ></v-text-field>
                  <v-btn class="me-8" variant="text" @click="onClickSeeAll">
                    <span class="text-decoration-underline text-none"
                      >See all</span
                    >
                  </v-btn>

                  <div class="d-inline-flex">
                    <v-btn
                      :disabled="page === 1"
                      icon="mdi-arrow-left"
                      size="small"
                      variant="tonal"
                      class="me-2"
                      @click="prevPage"
                    ></v-btn>

                    <v-btn
                      :disabled="page === pageCount"
                      icon="mdi-arrow-right"
                      size="small"
                      variant="tonal"
                      @click="nextPage"
                    ></v-btn>
                  </div>
                </div>
              </h4>
            </template>

            <template v-slot:default="{ items, isExpanded, toggleExpand }">
              <v-row>
                <v-col
                  v-for="(item, i) in items"
                  :key="item.raw.StationAdmin_ID"
                  class="mb-6"
                  cols="3"
                >
                  <v-card
                    max-width="440px"
                    class="bg-background rounded"
                    elevation="2"
                  >
                    <v-img
                      class="gradient-cover cover"
                      height="200px"
                      src="../../../assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
                      cover
                    ></v-img>

                    <v-row justify="center">
                      <v-col
                        align-self="start"
                        class="d-flex justify-center align-center pa-0 mt-n16"
                        cols="12"
                      >
                        <v-avatar
                          class="profile avatar-center-heigth avatar-shadow"
                          color="grey"
                          size="164"
                        >
                          <v-img
                            v-if="item.raw.Profile_Picture"
                            :src="
                              'http://backend.test/' + item.raw.Profile_Picture
                            "
                            cover
                          ></v-img>
                          <v-img
                            v-else
                            src="../../../assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg"
                            cover
                          ></v-img>
                        </v-avatar>
                      </v-col>
                    </v-row>

                    <v-list-item color="#0000" class="profile-text-name ma-4">
                      <v-list-item-content>
                        <v-row>
                          <v-col cols="10">
                            <v-list-item-title class="text-h6">
                              {{ item.raw.First_Name }} {{ item.raw.Last_Name }}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                              Station Admin
                            </v-list-item-subtitle>
                            <v-list-item-title>
                              <v-chip
                                :color="
                                  item.raw.Status === 'active'
                                    ? 'success'
                                    : 'red'
                                "
                                size="small"
                                label
                                class="mt-3"
                              >
                                {{ item.raw.Status }}
                              </v-chip>
                              <v-icon
                                x-small
                                color="error"
                                class="mt-3 ml-3"
                                @click="
                                  openConfirmDialog(item.raw.StationAdmin_ID)
                                "
                                >mdi-swap-horizontal-circle-outline</v-icon
                              >
                            </v-list-item-title>
                          </v-col>
                          <v-col
                            cols="2"
                            class="d-flex justify-end align-content-end"
                          >
                            <v-switch
                              :model-value="isExpanded(item)"
                              :color="isExpanded(item) ? 'success' : 'red'"
                              density="compact"
                              @click="() => toggleExpand(item)"
                            ></v-switch>
                          </v-col>
                        </v-row>

                        <v-expand-transition>
                          <div v-if="isExpanded(item)">
                            <v-container>
                              <v-divider></v-divider>
                            </v-container>

                            <!-- Additional Information Columns -->
                            <v-row class="fluid">
                              <v-col cols="6" class="mb-0 pb-0">
                                <v-list-item-title>
                                  <span class="font-weight-bold">Age:</span>
                                  {{ item.raw.Age }}
                                </v-list-item-title>
                              </v-col>

                              <v-col cols="6" class="mb-0 pb-0">
                                <v-list-item-title>
                                  <span class="font-weight-bold">Sex:</span>
                                  {{ item.raw.Sex }}
                                </v-list-item-title>
                                <!-- Add more items as needed -->
                              </v-col>

                              <v-col cols="12" class="mb-0 pb-0">
                                <v-list-item-title>
                                  <span class="font-weight-bold">Address:</span>
                                  {{ item.raw.Address }}
                                </v-list-item-title>
                              </v-col>

                              <v-col cols="12" class="mb-0 pb-0">
                                <v-list-item-title>
                                  <span class="font-weight-bold">Contact:</span>
                                  {{ item.raw.Admin_PhoneNum }}
                                </v-list-item-title>
                              </v-col>

                              <v-col cols="12" class="mb-0 pb-0">
                                <v-list-item-title>
                                  <span class="font-weight-bold"
                                    >Birthplace:</span
                                  >
                                  {{ item.raw.Birthplace }}
                                </v-list-item-title>
                              </v-col>
                              <!-- Add more columns as needed -->
                            </v-row>

                            <v-container>
                              <v-divider></v-divider>
                            </v-container>

                            <StationSelection
                              v-model="item.raw.Station_Name"
                              customDensity="compact"
                              customVariant="solo-inverted"
                              :showAppend="true"
                            />
                          </div>
                        </v-expand-transition>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
              </v-row>
            </template>
            <template v-slot:footer="{ page, pageCount }">
              <v-footer
                color="background"
                class="justify-space-between text-body-2 mt-4"
              >
                Total admins: {{ StationAdmins.length }}

                <div>Page {{ page }} of {{ pageCount }}</div>
              </v-footer>
            </template>
          </v-data-iterator>
        </v-window-item>
        <v-window-item value="TeacherAccounts">
          <v-card>
            <v-card-text>ss</v-card-text>
          </v-card>
        </v-window-item>
      </v-window>
    </v-card>
  </v-container>
  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>

<script>
import axios from "axios";
import { getStationAdminsWithStation } from "@/services/BackendApi"; // Assuming you have an API module or file
export default {
  data() {
    return {
      tab: null,
      StationAdmins: [], // Initialize as an empty array
      itemsPerPage: 4,
      search: "",
      selectedStation: "", // Initialize if used in StationSelection
      loading: false, // Initialize loading as false
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    async loadData() {
      console.log("Loading data...");
      this.loading = true;

      try {
        this.StationAdmins = await getStationAdminsWithStation();
        console.log("Data loaded successfully:", this.StationAdmins);
      } catch (error) {
        console.error("Failed to fetch station admins:", error);
      } finally {
        this.loading = false;
        console.log("Loading complete.");
      }
    },
    openConfirmDialog(id) {
      // Open the confirm dialog for approving or rejecting an application
      this.$refs.confirmationModal.dialog = true;
      this.$refs.confirmationModal.confirmAction = () => this.toggleStatus(id);
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

    onButtonClick() {
      // Handle button click
    },
    onFileChanged() {
      // Handle file change
    },
    saveBio() {
      // Handle saving bio
    },

    onClickSeeAll() {
      this.itemsPerPage =
        this.itemsPerPage === 4 ? this.StationAdmins.length : 4;
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
  top: 121px;
  color: cadetblue;
  background: blueviolet;
  background: rgb(125, 198, 163);
  background: linear-gradient(
    50deg,
    rgba(125, 198, 163, 1) 0%,
    rgba(35, 216, 227, 1) 72%
  );
}

.bg {
  background: rgb(255, 197, 185);
  background: linear-gradient(
      0deg,
      rgba(255, 197, 185, 0.711922268907563) 0%,
      rgba(220, 246, 223, 0.6671043417366946) 35%,
      rgba(255, 255, 255, 0.7539390756302521) 74%
    ),
    url(http://unblast.com/wp-content/uploads/2021/09/Real-Estate-Agent-Illustration.jpg);
}

.avatar-shadow {
  box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75); /* Adjust the color to PNP blue */
  -webkit-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
  -moz-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
}
</style>
