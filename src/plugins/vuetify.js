import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";

// Vuetify
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as labsComponents from "vuetify/labs/components";

const dark = {
  dark: true,
  colors: {
    background: "#1E1E1E", // Dark background color
    surface: "#333333", // Slightly lighter surface color
    primary: "#3399CC", // Lighter blue for better visibility in dark mode
    "primary-darken-1": "#001F4C", // Darkened PNP Blue
    secondary: "#C8102E", // PNP Red
    "secondary-darken-1": "#7F0914", // Darkened PNP Red
    error: "#B00020", // Standard error color
    info: "#2196F3", // Standard info color
    success: "#4CAF50", // Standard success color
    warning: "#FB8C00", // Standard warning color
  },
};

const light = {
  dark: false,
  colors: {
    background: "#F5F5F5", // White background
    surface: "#FFFFFF", // Light surface color
    primary: "#003366", // PNP Blue
    "primary-darken-1": "#001F4C", // Darkened PNP Blue
    secondary: "#C8102E", // PNP Red
    "secondary-darken-1": "#7F0914", // Darkened PNP Red
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
    defaultSet: "mdi", // Set the default icon set to 'mdi' (Material Design Icons)
  },
  theme: {
    themes: {
      dark,
      light,
    },
  },
});
