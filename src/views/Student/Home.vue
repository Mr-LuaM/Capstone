<template>
   <v-container fluid class="rounded-lg bg-surface">
    <v-row
        align="start"
        no-gutters
        style="height: 150px;"
      >
      <v-avatar
        color="grey"
        size="150"
        rounded="0"
      >
        <v-img cover src="@/assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg"></v-img>
      </v-avatar>
      <v-list-item
        class="text-black"
        title="Marcus Obrien"
        subtitle="Network Engineer"
      ></v-list-item>
   
      </v-row>

   </v-container>
</template>
<script>
export default {
  data() {
    return {
      userName: "John Doe",
      itemList: ["Item 1", "Item 2", "Item 3"],
    };
  },
};
</script>