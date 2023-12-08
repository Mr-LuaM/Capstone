<!-- <script setup>
import "@fullcalendar/core/vdom";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import ListPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";

const id = ref(10)

const options = reactive({
    plugins:[dayGridPlugin, timeGridPlugin, ListPlugin, interactionPlugin],
    initialView:'dayGridMonth',
    headerToolbar:{
        left: 'prev, next today'
        centerL ' title',
        right: 'dayGridMonth, dayGridWeek, listDay'
    },
    editable: true,
    selectable:true,
    weekends:true,
    select:(args)=>{
        id.value = value + 1

        const cal = arg.view.calendar
        cal.unselect()
        cal.newEvent({
            id:'$'
        })
    }
})
</script> -->
<template>
  <!-- // <FullCalendar v-bind:options="options"></FullCalendar> -->
</template>
