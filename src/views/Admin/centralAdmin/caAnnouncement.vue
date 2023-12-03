<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card>
      <v-toolbar color="primary-darken-1">
        <v-app-bar-nav-icon>
          <v-icon icon="mdi-post"></v-icon>
        </v-app-bar-nav-icon>
        &nbsp;
        <v-toolbar-title>Manage Announcements</v-toolbar-title>

        <v-spacer></v-spacer>

        <template v-slot:extension>
          <v-tabs v-model="tab" centered bg-color="primary" grow>
            <v-tab value="Announcement">Announcement</v-tab>
          </v-tabs>
        </template>
      </v-toolbar>

      <v-window v-model="tab">
        <v-window-item value="Announcement" class="pa-8">
          <v-data-iterator
            :items="announcements"
            :items-per-page="itemsPerPage"
            :search="search"
            :item-value="(item) => item.id"
          >
            <template v-slot:header="{ page, pageCount, prevPage, nextPage }">
              <h4
                class="text-h6 font-weight-bold d-flex justify-space-between mb-4 align-center"
              >
                <div class="text-truncate">Announcements</div>

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
                  <v-col cols="auto">
                    <v-btn
                      variant="flat"
                      @click="dialog = true"
                      color="secondary"
                    >
                      Add New
                    </v-btn>
                  </v-col>
                </div>
              </h4>
            </template>

            <template v-slot:default="{ items, isExpanded, toggleExpand }">
              <v-card
                v-for="announcement in items"
                :key="announcement.id"
                class="mx-auto"
                variant="flat"
              >
                <v-img
                  v-if="announcement.raw.picture_url === null"
                  height="200"
                  src="@/assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
                  cover
                >
                  <v-toolbar color="rgba(0, 0, 0, 0)">
                    <v-toolbar-title class="text-h6 text-white">
                      {{ announcement.raw.title }}
                    </v-toolbar-title>

                    <template v-slot:append>
                      <v-menu transition="scale-transition">
                        <template v-slot:activator="{ props }">
                          <v-btn
                            color="black"
                            icon="mdi-dots-vertical"
                            v-bind="props"
                          >
                          </v-btn>
                        </template>
                        <v-list class="pa-0">
                          <v-list-item>
                            <v-btn
                              class="d-inline-flex align-center"
                              block
                              prepend-icon="mdi-pencil"
                              variant="plain"
                              color="secondary"
                              @click="editAnnouncement(announcement)"
                            >
                              Edit
                            </v-btn>
                          </v-list-item>
                          <v-list-item>
                            <v-btn
                              block
                              prepend-icon="mdi-delete"
                              variant="plain"
                              color="primary"
                              @click="openConfirmDialog(announcement.raw.id)"
                              >Delete</v-btn
                            >
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </v-toolbar>
                </v-img>
                <v-img
                  v-else
                  height="200"
                  :src="`http://backend.test/${announcement.raw.picture_url}`"
                  cover
                >
                  <v-toolbar color="rgba(0, 0, 0, 0)">
                    <v-toolbar-title class="text-h6 text-white">
                      {{ announcement.raw.title }}
                    </v-toolbar-title>

                    <template v-slot:append>
                      <v-menu transition="scale-transition">
                        <template v-slot:activator="{ props }">
                          <v-btn
                            color="black"
                            icon="mdi-dots-vertical"
                            v-bind="props"
                          >
                          </v-btn>
                        </template>
                        <v-list class="pa-0">
                          <v-list-item>
                            <v-btn
                              class="d-inline-flex align-center"
                              block
                              prepend-icon="mdi-pencil"
                              variant="plain"
                              color="secondary"
                              @click="editAnnouncement(announcement)"
                            >
                              Edit
                            </v-btn>
                          </v-list-item>
                          <v-list-item>
                            <v-btn
                              block
                              prepend-icon="mdi-delete"
                              variant="plain"
                              color="primary"
                              @click="openConfirmDialog(announcement.raw.id)"
                              >Delete</v-btn
                            >
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </v-toolbar>
                </v-img>

                <v-card-text>
                  <div class="d-inline-flex align-center justify-center">
                    <v-avatar
                      color="grey-darken-3"
                      :image="
                        announcement.raw.Profile_Picture
                          ? `http://backend.test/${announcement.raw.Profile_Picture}`
                          : '@/assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg'
                      "
                    ></v-avatar>

                    <v-list-item-title class="ml-2">
                      {{
                        `${announcement.raw.First_Name} ${announcement.raw.Last_Name}`
                      }}
                    </v-list-item-title>

                    <v-list-item-subtitle class="ml-2"
                      >Main Admin</v-list-item-subtitle
                    >
                  </div>

                  <div class="mt-5">{{ announcement.raw.content }}</div>

                  <div>{{ announcement.raw.updated_at }}</div>
                </v-card-text>
              </v-card>
            </template>

            <template v-slot:footer="{ page, pageCount }">
              <v-footer
                color="background"
                class="justify-space-between text-body-2 mt-4"
              >
                Total announcements: {{ announcements.length }}
                <div>Page {{ page }} of {{ pageCount }}</div>
              </v-footer>
            </template>
          </v-data-iterator>
        </v-window-item>
      </v-window>
    </v-card>

    <v-dialog v-model="dialog" persistent width="800">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{
            isEditing ? "Edit Announcement" : "Add Announcement"
          }}</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" @submit.prevent="submit">
              <v-row justify="center" align="center">
                <!-- Add your form fields and logic here -->
                <!-- For example: -->
                <v-col cols="12">
                  <GenericTextField
                    v-model="editedAnnouncement.title"
                    label="Title"
                    required
                  />
                </v-col>
                <v-col cols="12">
                  <GenericTextArea
                    v-model="editedAnnouncement.content"
                    label="Content"
                    required
                  />
                </v-col>
                <v-col cols="12">
                  <ImageInput
                    v-model="picture_url"
                    label="Image"
                    customDensity="default"
                    @change="handleFileChange"
                  />
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            v-if="isEditing"
            color="secondary"
            variant="plain"
            @click="closeDialog"
          >
            Close
          </v-btn>
          <v-btn
            v-else
            color="secondary"
            variant="plain"
            @click="this.dialog = false"
          >
            Close
          </v-btn>
          <v-btn
            v-if="isEditing"
            variant="text"
            @click="saveEditedAnnouncement"
            color="primary"
          >
            Save Changes
          </v-btn>
          <v-btn v-else variant="text" @click="addAnnouncement" color="primary">
            Add Announcement
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

  <!-- Additional components such as infoSnack and confirmationModal go here -->
</template>

<script>
import axios from "axios";
import { getAnnouncements } from "@/services/BackendApi";

export default {
  data() {
    return {
      dialog: false,
      tab: null,
      announcements: [],
      itemsPerPage: 4,
      search: "",
      editedAnnouncement: {},
      originaleditedAnnouncement: {},
      formTitle: "",
      isEditing: false,
      loading: false,
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    openConfirmDialog(id) {
      // Open the confirm dialog for approving or rejecting an application
      this.$refs.confirmationModal.dialog = true;
      this.$refs.confirmationModal.confirmAction = () =>
        this.removeAnnouncement(id);
    },
    async removeAnnouncement(id) {
      try {
        const secureTokenResponse = await axios.get(
          "generateSecureToken/" + id
        );
        const secureToken = secureTokenResponse.data;
        // Use axios or your preferred HTTP library to send a request to your backend
        const response = await axios.post("removeAnnouncement/" + id, {
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
    handleFileChange(event) {
      this.picture_url = event.target.files[0];
    },
    async loadData() {
      console.log("Loading data...");
      this.loading = true;

      try {
        this.announcements = await getAnnouncements();
      } catch (error) {
        console.error("Failed to fetch announcements:", error);
      } finally {
        this.loading = false;
        console.log("Loading complete.");
      }
    },
    editAnnouncement(announcement) {
      this.dialog = true;
      this.editedAnnouncement = { ...announcement.raw };
      this.originaleditedAnnouncement = { ...announcement.raw }; // Store the original value
      console.log(this.editedAnnouncement);
    },
    closeDialog() {
      this.dialog = false;
      this.editedAnnouncement = { ...this.originaleditedAnnouncement }; // Restore the original value
    },
    async saveEditedAnnouncement() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        try {
          // Create a FormData object
          const formData = new FormData();

          // Append the id to the picture_url
          formData.append("title", this.editedAnnouncement.title);
          formData.append("id", this.editedAnnouncement.id);
          formData.append("content", this.editedAnnouncement.content);
          formData.append("picture_url", this.picture_url);

          // Make the Axios request to update the announcement
          const response = await axios.post(`/updateAnnouncement`, formData);

          // Check the response and handle accordingly
          if (response.data.success === true) {
            // Show a success alert or perform other success-related actions here
            this.dialog = false;
            this.$refs.snackbar.openSnackbar(response.data.message, "success");
            this.loadData(); // Reload the data after successful update
          } else {
            // Show the error Snackbar
            this.$refs.snackbar.openSnackbar(response.data.message, "error");
          }
        } catch (error) {
          console.error("Error updating announcement:", error);
          // Handle errors as needed
        }
      }
    },
    async addAnnouncement() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        try {
          // Create a FormData object
          const formData = new FormData();

          // Append the id to the picture_url

          formData.append("user_ID", this.$store.state.userId);
          formData.append("title", this.editedAnnouncement.title);
          formData.append("id", this.editedAnnouncement.id);
          formData.append("content", this.editedAnnouncement.content);
          formData.append("picture_url", this.picture_url);

          // Make the Axios request to update the announcement
          const response = await axios.post(`/addAnnouncement`, formData);

          // Check the response and handle accordingly
          if (response.data.success === true) {
            // Show a success alert or perform other success-related actions here
            this.dialog = false;
            this.$refs.snackbar.openSnackbar(response.data.message, "success");
            this.loadData(); // Reload the data after successful update
          } else {
            // Show the error Snackbar
            this.$refs.snackbar.openSnackbar(response.data.message, "error");
          }
        } catch (error) {
          console.error("Error adding announcement:", error);
          // Handle errors as needed
        }
      }
    },
  },

  computed: {
    isEditing() {
      console.log("editedAnnouncement.raw.id:", this.editedAnnouncement.id);
      return Boolean(this.editedAnnouncement.id);
    },
  },
  // Add other methods for handling announcements as needed
};
</script>
