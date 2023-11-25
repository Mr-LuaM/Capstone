import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";

// Vuetify
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as labsComponents from "vuetify/labs/components";

const dark = {
  dark: true,
  colors: {
    background: "#121212", // Darker background color
    surface: "#1E1E1E", // Dark surface color
    primary: "#EF5350", // PNP Red as the primary color
    "primary-darken-1": "#C8102E", // Darkened PNP Red
    secondary: "#1976D2", // ONP Blue as the secondary color
    "secondary-darken-1": "#003366", // Darkened ONP Blue
    error: "#FF5252", // Lighter error color
    info: "#2196F3", // Standard info color
    success: "#66BB6A", // Lighter success color
    warning: "#FFB74D", // Lighter warning color
  },
};

const light = {
  dark: false,
  colors: {
    background: "#F5F5F5", // White background
    surface: "#FFFFFF", // Light surface color
    primary: "#EF5350", // PNP Red as the primary color
    "primary-darken-1": "#C8102E", // Darkened PNP Red
    secondary: "#1976D2", // ONP Blue as the secondary color
    "secondary-darken-1": "#003366", // Darkened ONP Blue
    error: "#B00020", // Standard error color
    info: "#2196F3", // Standard info color
    success: "#4CAF50", // Standard success color
    warning: "#FB8C00", // Standard warning color
  },
};

export default createVuetify({
  components: {
    ...components,
    ...labsComponents,
  },
  icons: {
    defaultSet: "mdi",
  },
  theme: {
    themes: {
      dark,
      light,
    },
  },
});
