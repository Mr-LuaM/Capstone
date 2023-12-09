import axios from "axios";

export async function getCourses(status = "all") {
  try {
    const url = status === "all" ? "getCourses" : `getCourses?status=${status}`;
    console.log(url);
    const courses = await axios.get(url);
    return courses.data; // Return the data
  } catch (error) {
    console.error(error);
    throw error; // Rethrow the error
  }
}

export async function getStations(status = "all") {
  try {
    // Use a dynamic URL based on the provided status parameter
    const url =
      status === "all" ? "getStations" : `getStations?status=${status}`;

    const stations = await axios.get(url);
    return stations.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}

export async function getStation() {
  try {
    const station = await axios.get("getStation");
    return station.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}
export async function getCourse() {
  try {
    const course = await axios.get("getCourse");
    return course.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}

export async function getApplicants() {
  try {
    const applicants = await axios.get("getApplicants");
    return applicants.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}

export async function getStationAdminsWithStation() {
  try {
    const applicants = await axios.get("getStationAdminsWithStation");
    return applicants.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}

export async function getRoles(userRole) {
  try {
    const roles = await axios.post("getRoles", { userRole });
    return roles.data; // Return the data
  } catch (error) {
    console.error("Failed to fetch roles:", error);
    throw error; // Rethrow the error
  }
}

export async function getAnnouncements() {
  try {
    const announcements = await axios.get("getAnnouncements");
    return announcements.data; // Return the data
  } catch (error) {
    console.error("Failed to fetch roles:", error);
    throw error; // Rethrow the error
  }
}
export async function getTeacherAssignmentsDetails() {
  try {
    const teachers = await axios.get("getTeacherAssignmentsDetails");
    return teachers.data; // Return the data
  } catch (error) {
    console.error("Failed to fetch roles:", error);
    throw error; // Rethrow the error
  }
}
export const getCoursesInStation = async (stationId) => {
  try {
    const response = await axios.get(`getCoursesInStation/${stationId}`);
    return response.data;
  } catch (error) {
    throw error;
  }
};
