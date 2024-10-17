export async function isAuthenticated() {
  try {
    const response = await fetch('http://localhost:8000/api/cookie.php', {
      method: 'GET',
      credentials: 'include', // Ensure that cookies are included in the request
    });

    const data = await response.json();
    if (data.status === 'success' && data.session_id) {
      return true; // User is authenticated
    } else {
      return false; // No active session, user is not authenticated
    }
  } catch (error) {
    return false; // In case of an error, assume user is not authenticated
  }
}
