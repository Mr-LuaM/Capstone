<template>
    <div class="text-center">
      <v-menu
        v-model="menu"
        :close-on-content-click="false"
        location="bottom right"
        max-width="250px"
      >
        <template v-slot:activator="{ props }">
           
            <v-btn icon v-bind="props">
                <v-badge :content="unreadCount" color="success">
  <v-icon>mdi-bell</v-icon>
</v-badge>

      </v-btn>
        </template>
  
        <v-card min-width="300">
            <v-list
      :items="items"
      item-props
      lines="three"
    >
      <template v-slot:subtitle="{ subtitle }">
        <div v-html="subtitle"></div>
      </template>
    </v-list>
        </v-card>
      </v-menu>
    </div>
  </template>
 <script>
 import { getAnnouncements } from '@/services/BackendApi';
 import { getMessaging, onMessage } from 'firebase/messaging';

 
 export default {
  data: () => ({
    fav: true,
      menu: false,
      message: false,
      hints: true,
    items: [], // Initialize as an empty array
    unreadCount: 0,
  }),

  created() {
    this.loadData();
    this.initializeFirebaseMessaging();
  },
  watch: {
  menu(newValue) {
    if (!newValue) { // When menu is closed
      this.unreadCount = 0;
    }
  }
},

  methods: {
    async loadData() {
      console.log("Loading data...");
      this.loading = true;

      try {
        const announcements = await getAnnouncements();
        this.items = [this.createDateSubheader(), ...this.formatAnnouncements(announcements)];
      } catch (error) {
        console.error("Failed to fetch announcements:", error);
      } finally {
        this.loading = false;
        console.log("Loading complete.");
      }
    },
    initializeFirebaseMessaging() {
      const messaging = getMessaging();
      onMessage(messaging, (payload) => {
        console.log('Foreground message received: ', payload);
        this.handleForegroundNotification(payload);
      });
    },

    handleForegroundNotification(payload) {
  console.log('Foreground message received: ', payload);

  // Handle the payload as needed
  // ...

  // Refresh the announcements list
  this.loadData();

  // Increment unread notifications count
  this.unreadCount += 1;
},




    createDateSubheader() {
      const today = new Date();
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const formattedDate = today.toLocaleDateString('en-US', options);
      return { type: 'subheader', title: formattedDate };
    },

    formatAnnouncements(announcements) {
      const divider = { type: 'divider', inset: true };
      return announcements.flatMap(announcement => ([
        {
            prependAvatar: announcement.Profile_Picture ? `http://backend.test//${announcement.Profile_Picture}` : '../../assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg',
          title: announcement.title,
          subtitle: `<span class="text-primary">${announcement.First_Name} ${announcement.Last_Name}</span> &mdash; ${announcement.content}`,
        },
        divider // Add a divider after each announcement
      ]));
    },
  },
}
</script>