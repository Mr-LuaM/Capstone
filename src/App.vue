<template>
  <router-view />
</template>

<script setup>
import store from '@/store'; 
import axios from 'axios';
// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging, getToken,onMessage } from "firebase/messaging";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyAgBNr4511jtM16IKwtCUDuHL2SZVE5y2s",
  authDomain: "push-notifcation-99402.firebaseapp.com",
  projectId: "push-notifcation-99402",
  storageBucket: "push-notifcation-99402.appspot.com",
  messagingSenderId: "111047369335",
  appId: "1:111047369335:web:d1e8ade7bd132d4e7d5c72",
  measurementId: "G-456GWRH1WY"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);



// Get registration token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
const messaging = getMessaging();
onMessage(messaging, (payload) => {
  console.log('Message received. ', payload);
  // ...
});

getToken(messaging, { vapidKey: 'BItLmAyAHXJOu-UTNLof3mhxYjJkprO-3OnTYnLUAFqkOlr942RLdytyPwU0CMiojif1DHZtFe-lVGgALrZkEok' }).then((currentToken) => {
  if (currentToken) {
    const userId = store.getters.userId; // Get the userId from Vuex store
    // Send the token and userId to your server

    if(!this.$route.path === 'login'){
    axios.post('/save-token', {
      fcm_token: currentToken,
      user_id: userId
    });
  }
    console.log('ct',currentToken);
  } else {
    console.log('No registration token available. Request permission to generate one.');
  }
}).catch((err) => {
  console.log('An error occurred while retrieving token. ', err);
  // ...
});
</script>
