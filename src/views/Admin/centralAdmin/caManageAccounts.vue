<template>
    <v-data-iterator :items="[1, 2, 3, 4, 5]"
    :items-per-page="itemsPerPage"
    :search="search">
    
        <template v-slot:header="{ page, pageCount, prevPage, nextPage }">
           
          <v-text-field
            v-model="search"
            clearable
            density="comfortable"
            hide-details
            placeholder="Search"
            prepend-inner-icon="mdi-magnify"
            style="max-width: 300px;"
            variant="solo"
          ></v-text-field>
      

        <div class="d-flex align-center">
          <v-btn
            class="me-8"
            variant="text"
            @click="onClickSeeAll"
          >
            <span class="text-decoration-underline text-none">See all</span>
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
    
    </template>
      <template v-slot:default="{ items, isExpanded, toggleExpand }">
        <v-row>
          <v-col v-for="(item, i) in items" :key="i" class="mb-6" cols="3">
            <v-card max-width="440px" class="bg-background rounded" elevation="2">
              <v-img
                class="gradient-cover cover"
                height="200px"
                src="../../../assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
                cover
              ></v-img>
  
              <v-row justify="center">
                <v-col align-self="start" class="d-flex justify-center align-center pa-0 mt-n16" cols="12">
                  <v-avatar class="profile avatar-center-heigth avatar-shadow" color="grey" size="164">
                    <v-img src="https://cdn.vuetifyjs.com/images/profiles/marcus.jpg" cover></v-img>
                  </v-avatar>
                </v-col>
              </v-row>
  
              <v-list-item color="#0000" class="profile-text-name ma-4">
                <v-list-item-content>
                    <v-row>
  <v-col cols="10">
    <v-list-item-title class="text-h6">Marcus Obrien</v-list-item-title>
    <v-list-item-subtitle>Station Admin</v-list-item-subtitle>
    <v-list-item-title>
      <v-chip color="success" size="small" label class="mt-3" >Active</v-chip>
      <v-icon x-small color="error" class="mt-3 ml-3" >mdi-swap-horizontal-circle-outline</v-icon>
    </v-list-item-title>
  </v-col>
  <v-col cols="2" class="d-flex justify-end align-content-end">
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
                      <!-- First Column -->
                      <v-col cols="6">
                        <v-list-item-title>Age: 25</v-list-item-title>
                      
                        <v-list-item-title>Address: 123 Main St</v-list-item-title>
                        <v-list-item-title>Contact: </v-list-item-title>
                        <v-list-item-title>Birthplace:</v-list-item-title> <!-- Replace with actual values -->
                        <v-list-item-title>Birthday:</v-list-item-title> <!-- Replace with actual values -->
                        <v-list-item-title>Nationality:</v-list-item-title> <!-- Replace with actual values -->
                        <!-- Add more items as needed -->
                      </v-col>
                      <!-- Second Column -->
                      <v-col cols="6">
                        <v-list-item-title>Sex: Male</v-list-item-title>
                      
                        <!-- Add more items as needed -->
                      </v-col>
                    </v-row>
  
                    <v-container>
                      <v-divider></v-divider>
                    </v-container>
  
         
  
                    <RolesAutocomplete customDensity="compact" customVariant="solo-inverted" />
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
        color="surface-variant"
        class="justify-space-between text-body-2 mt-4"
      >
        Total mice: {{ 5}}

        <div>
          Page {{ page }} of {{ pageCount }}
        </div>
      </v-footer>
    </template>
    </v-data-iterator>
  </template>
  
  <script>
  export default {
    data() {
      return {
        itemsPerPage: 4,
        editBio: false,
        bioIcon: 'your_icon', // replace with the actual icon
        Bio: 'Your initial bio',
        Phone: 'Your initial phone number',
      };
    },
    methods: {
      onButtonClick() {
        // Handle button click
      },
      onFileChanged() {
        // Handle file change
      },
      saveBio() {
        // Handle saving bio
      },

      onClickSeeAll () {
        this.itemsPerPage = this.itemsPerPage === 4 ? this.mice.length : 4
    
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
    background: linear-gradient(to right, hsla(210, 100%, 50%, 1) 0%, hsla(0, 100%, 50%, 1) 35%, hsla(0, 100%, 50%, 1) 74%);
    opacity: 0.7; /* Adjust the opacity as needed */
  }
  
  .upload-btn {
    position: absolute !important;
    z-index: 999;
    top: 121px;
    color: cadetblue;
    background: blueviolet;
    background: rgb(125, 198, 163);
    background: linear-gradient(50deg, rgba(125, 198, 163, 1) 0%, rgba(35, 216, 227, 1) 72%);
  }
  
  .bg {
    background: rgb(255, 197, 185);
    background: linear-gradient(0deg, rgba(255, 197, 185, 0.711922268907563) 0%, rgba(220, 246, 223, 0.6671043417366946) 35%, rgba(255, 255, 255, 0.7539390756302521) 74%),
      url(http://unblast.com/wp-content/uploads/2021/09/Real-Estate-Agent-Illustration.jpg);
  }
  
  .avatar-shadow {
    box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75); /* Adjust the color to PNP blue */
    -webkit-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
    -moz-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
  }
  </style>
  