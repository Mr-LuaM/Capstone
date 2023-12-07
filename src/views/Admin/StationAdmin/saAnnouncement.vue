<template>
    <v-container fluid class="rounded-lg bg-surface">
     
       
  
        <v-window v-model="tab">
          <v-window-item value="Announcement" class="pa-3">
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
                    <br>
                    <br>
                    <br>
                    <br>
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

  
     
         
    </v-container>

  
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
  