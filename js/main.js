// Constants
const PRAYER_TIMES_API = 'https://api.aladhan.com/v1/timings';
const QURAN_API = 'https://api.alquran.cloud/v1/ayah/random';

// Global Variables
let userLocation = null;
let prayerTimes = null;

// Date/Time Functions
function updateDateTime() {
    const now = new Date();
    const gregorianDate = document.getElementById('gregorian-date');
    const hijriDate = document.getElementById('hijri-date');
    
    if (gregorianDate) {
        gregorianDate.textContent = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    if (hijriDate) {
        const islamicDate = new Intl.DateTimeFormat('en-US-u-ca-islamic', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(now);
        hijriDate.textContent = islamicDate;
    }
}

// Initialize the application
document.addEventListener('DOMContentLoaded', () => {
    console.log('App initialized');
    console.log('Current page elements:', {
        arabicVerse: !!document.getElementById('arabic-verse'),
        hijriDate: !!document.getElementById('hijri-date'),
        prayerCards: !!document.querySelector('.prayer-cards-container'),
        location: !!document.getElementById('location')
    });
    
    initializeLocation();
    updateDateTime();
    setInterval(updateDateTime, 1000);
    setInterval(updatePrayerTimes, 60000); // Update prayer times every minute
    
    // Only load daily verse if we're on the Quran page
    if (document.getElementById('arabic-verse')) {
        loadDailyVerse();
    }
    
    // Only update Islamic date and events if we're on the Calendar page
    if (document.getElementById('hijri-date')) {
        updateIslamicDate();
        loadUpcomingEvents();
    }
    
    // Only initialize prayer tracking if we're on the Tracking page
    if (document.querySelector('.prayer-cards-container')) {
        initializePrayerTracking();
    }
});

function updatePrayerTimes() {
    if (userLocation) {
        console.log('Updating prayer times...');
        fetchPrayerTimes();
    } else {
        console.log('No location available for prayer times update');
    }
}

// Location Functions
function initializeLocation() {
    console.log('Initializing location...');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                console.log('Location obtained:', position);
                userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                updateLocationDisplay();
                fetchPrayerTimes();
            },
            error => {
                console.error('Geolocation error:', error);
                // Fallback to IP-based location
                fetchPrayerTimesByIP();
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        console.error('Geolocation is not supported by this browser');
        // Fallback to IP-based location
        fetchPrayerTimesByIP();
    }
}

async function fetchPrayerTimesByIP() {
    try {
        console.log('Fetching location by IP using backend proxy...');
        
        // Fix the API path based on current page location
        let apiUrl = '';
        
        // Check if we're in a subdirectory
        if (window.location.pathname.includes('/php/')) {
            apiUrl = '../api/prayer_times.php?action=ip_location';
        } else {
            apiUrl = 'api/prayer_times.php?action=ip_location';
        }
        
        console.log('IP location API URL:', apiUrl);
        
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error(`Failed to fetch location from backend: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        console.log('IP location data:', data);
        
        if (data.latitude && data.longitude) {
            userLocation = {
                latitude: data.latitude,
                longitude: data.longitude
            };
            updateLocationDisplay();
            fetchPrayerTimes();
        } else {
            throw new Error('Could not determine location from IP');
        }
    } catch (error) {
        console.error('IP location error:', error);
        showPrayerTimesError('Could not determine your location. Please try enabling location services.');
    }
}

async function fetchPrayerTimes() {
    if (!userLocation) {
        console.error('No location data available');
        showPrayerTimesError('Location information is not available. Please enable location access to see prayer times for your area.');
        return;
    }

    try {
        console.log('Fetching prayer times for location:', userLocation);
        const date = new Date();
        const params = new URLSearchParams({
            latitude: userLocation.latitude,
            longitude: userLocation.longitude,
            method: 2, // Islamic Society of North America (ISNA)
            month: date.getMonth() + 1,
            year: date.getFullYear(),
            day: date.getDate()
        });

        const url = `${PRAYER_TIMES_API}?${params.toString()}`;
        console.log('Requesting prayer times from:', url);

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Prayer times API Response:', data);
        
        if (data.code === 200 && data.data && data.data.timings) {
            prayerTimes = data.data.timings;
            updatePrayerTimesDisplay();
        } else {
            throw new Error('Invalid data received from prayer times service');
        }
    } catch (error) {
        console.error('Prayer times fetch error:', error);
        showPrayerTimesError(`Error fetching prayer times: ${error.message}`);
    }
}

function updatePrayerTimesDisplay() {
    if (!prayerTimes) {
        console.error('No prayer times available');
        return;
    }

    console.log('Updating prayer times display:', prayerTimes);

    const prayers = [
        { id: 'imsak', key: 'Imsak', displayName: 'Ishrak' }, // Changed display name to Ishrak
        { id: 'fajr', key: 'Fajr', displayName: 'Fajr' },
        { id: 'sunrise', key: 'Sunrise', displayName: 'Sunrise' },
        { id: 'dhuhr', key: 'Dhuhr', displayName: 'Dhuhr' },
        { id: 'asr', key: 'Asr', displayName: 'Asr' },
        { id: 'maghrib', key: 'Maghrib', displayName: 'Maghrib' },
        { id: 'isha', key: 'Isha', displayName: 'Isha' },
        { id: 'midnight', key: 'Midnight', displayName: 'Midnight' }
    ];

    prayers.forEach(prayer => {
        const element = document.getElementById(prayer.id);
        if (element && prayerTimes[prayer.key]) {
            try {
                const parentCard = element.closest('.prayer-time-card');
                if (parentCard) {
                    const titleElement = parentCard.querySelector('h6');
                    if (titleElement) {
                        titleElement.textContent = prayer.displayName;
                    }
                }
                element.textContent = formatTime(prayerTimes[prayer.key]);
            } catch (error) {
                console.error(`Error formatting time for ${prayer.id}:`, error);
                element.textContent = prayerTimes[prayer.key];
            }
        }
    });
}

function formatTime(timeStr) {
    try {
        const [hours, minutes] = timeStr.split(':').map(num => parseInt(num, 10));
        const date = new Date();
        date.setHours(hours);
        date.setMinutes(minutes);
        return date.toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    } catch (error) {
        console.error('Error formatting time:', error);
        return timeStr; // Return original time string if formatting fails
    }
}

function updateLocationDisplay() {
    const locationElement = document.getElementById('location');
    if (locationElement && userLocation) {
        locationElement.textContent = 
            `Latitude: ${userLocation.latitude.toFixed(4)}, Longitude: ${userLocation.longitude.toFixed(4)}`;
    }
}

// Islamic Calendar Functions
function updateIslamicDate() {
    const date = new Date();
    const hijriDateElement = document.getElementById('hijri-date');
    
    if (hijriDateElement) {
        try {
            const islamicDate = new Intl.DateTimeFormat('en-US-u-ca-islamic', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).format(date);
            hijriDateElement.textContent = islamicDate;
        } catch (error) {
            console.error('Error formatting Islamic date:', error);
            hijriDateElement.textContent = 'Error loading Islamic date';
        }
    }
}

function loadUpcomingEvents() {
    const eventsList = document.getElementById('events-list');
    if (!eventsList) return;

    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth();
    const currentDay = new Date().getDate();

    // Islamic events for the year
    const events = [
        { date: '2025-03-10', name: 'Ramadan Begins' },
        { date: '2025-04-09', name: 'Eid al-Fitr' },
        { date: '2025-06-16', name: 'Eid al-Adha' },
        { date: '2025-07-07', name: 'Islamic New Year' },
        { date: '2025-08-15', name: 'Ashura' },
        { date: '2025-09-15', name: "Prophet Muhammad's Birthday" }
    ];

    // Filter and sort upcoming events
    const upcomingEvents = events
        .filter(event => {
            const eventDate = new Date(event.date);
            return eventDate >= new Date();
        })
        .sort((a, b) => new Date(a.date) - new Date(b.date));

    if (upcomingEvents.length === 0) {
        eventsList.innerHTML = '<li class="text-muted">No upcoming events for this year</li>';
        return;
    }

    eventsList.innerHTML = upcomingEvents
        .map(event => {
            const eventDate = new Date(event.date);
            const formattedDate = eventDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            return `
                <li class="mb-2">
                    <strong>${event.name}</strong><br>
                    <span class="text-muted">${formattedDate}</span>
                </li>
            `;
        })
        .join('');
}

// Quran Functions
async function loadDailyVerse() {
    const arabicVerse = document.getElementById('arabic-verse');
    const verseTranslation = document.getElementById('verse-translation');
    
    if (!arabicVerse || !verseTranslation) return;

    try {
        console.log('Fetching daily verse...');
        // Fix the API path based on current page location
        let apiUrl = '';
        
        // Check if we're in a subdirectory
        if (window.location.pathname.includes('/php/')) {
            apiUrl = '../api/prayer_times.php?action=quran_verse';
        } else {
            apiUrl = 'api/prayer_times.php?action=quran_verse';
        }
        
        console.log('Quran verse API URL:', apiUrl);
        
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error(`Failed to fetch Quran verse: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Quran verse API Response:', data);

        if (data.data) {
            const verse = data.data;
            arabicVerse.innerHTML = `
                <div class="verse-arabic" style="text-align: right; margin-right: 20px; padding: 10px;">
                    <i class="fas fa-quran me-2"></i>
                    ${verse.text}
                </div>
                <div class="verse-surah mt-2" style="text-align: right; margin-right: 20px; padding: 10px;">
                    <i class="fas fa-bookmark me-2"></i>
                    <span class="surah-name">${verse.surah}</span>
                </div>
            `;
            verseTranslation.innerHTML = `
                <div class="verse-translation">
                    <i class="fas fa-language me-2"></i>
                    ${verse.translation || verse.englishTranslation || 'Translation not available'}
                </div>
                <div class="verse-reference mt-2 text-muted" style="text-align: right; margin-right: 20px; padding: 10px;">
                    <small>${verse.reference}</small>
                </div>
            `;
        } else {
            throw new Error('Invalid verse data received');
        }
    } catch (error) {
        console.error('Error loading Quran verse:', error);
        arabicVerse.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Unable to load daily verse. Please try again later.
            </div>
        `;
        verseTranslation.innerHTML = '';
    }
}

// Prayer Tracking Functions
const API_ENDPOINTS = {
    get prayer_tracking() {
        // Dynamic path based on current page location
        return window.location.pathname.includes('/php/') ? '../api/api.php' : 'api/api.php';
    },
    get prayer_times() {
        // Dynamic path based on current page location
        return window.location.pathname.includes('/php/') ? '../api/prayer_times.php' : 'api/prayer_times.php';
    }
};

// Helper function to safely make API requests with retry
async function safeApiRequest(url, options = {}, retries = 2) {
    try {
        console.log(`Making API request to ${url}${retries < 2 ? ' (retry attempt)' : ''}`);
        
        // Set a timeout for the request to prevent long hanging requests
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
        
        // Add the signal to the options
        const requestOptions = {
            ...options,
            signal: controller.signal
        };
        
        const response = await fetch(url, requestOptions);
        clearTimeout(timeoutId); // Clear the timeout if request completes
        
        // First check if we got a valid response status
        if (!response.ok) {
            // Try to get meaningful error info from response
            let errorText = '';
            try {
                errorText = await response.text();
                console.error('Server error response:', errorText);
            } catch (e) {
                console.error('Could not read error response:', e);
            }
            
            throw new Error(`HTTP error ${response.status}: ${response.statusText}`);
        }
        
        // Try to parse as JSON
        const text = await response.text();
        try {
            // Check if the response is empty
            if (!text.trim()) {
                throw new Error('Empty response received from server');
            }
            
            return JSON.parse(text);
        } catch (parseError) {
            console.error('Invalid JSON response:', text);
            
            // Check if the response text contains HTML or PHP error
            const isHtmlResponse = text.includes('<!DOCTYPE') || text.includes('<html') || text.includes('<?php');
            if (isHtmlResponse) {
                console.error('Server returned HTML instead of JSON');
                
                // If response contains PHP Fatal error or Warning, extract it
                const errorMatch = text.match(/<b>(?:Fatal error|Warning|Parse error|Notice)<\/b>:(.*?)(?:<br|\n)/i);
                if (errorMatch && errorMatch[1]) {
                    throw new Error(`PHP Error: ${errorMatch[1].trim()}`);
                }
                
                throw new Error('Server returned HTML instead of JSON. The API might not be properly configured.');
            }
            
            throw new Error('Server returned invalid JSON response');
        }
    } catch (error) {
        console.error(`API request failed: ${error.message}`);
        
        // If we have retries left and it's not an abort error, try again
        if (retries > 0 && error.name !== 'AbortError') {
            console.log(`Retrying request to ${url}. Attempts remaining: ${retries}`);
            
            // Wait for a brief moment before retrying (incremental backoff)
            await new Promise(resolve => setTimeout(resolve, 1000 * (3 - retries)));
            
            return safeApiRequest(url, options, retries - 1);
        }
        
        // No retries left or abort error - propagate the error
        throw error;
    }
}

async function initializePrayerTracking() {
    console.log('Initializing prayer tracking...');
    
    // Get or create a user ID (for demo purposes, create one if not exists)
    let userId = localStorage.getItem('user_id');
    if (!userId) {
        userId = 'user_' + Math.random().toString(36).substring(2, 15);
        localStorage.setItem('user_id', userId);
    }
    console.log('Using user ID:', userId);
    
    // Debug: Show paths
    console.log('Current pathname:', window.location.pathname);
    console.log('API endpoints:', {
        prayer_tracking: API_ENDPOINTS.prayer_tracking,
        prayer_times: API_ENDPOINTS.prayer_times
    });
    
    // First verify API connectivity
    try {
        // Try to connect to our test endpoint first
        let testEndpoint;
        if (window.location.pathname.includes('/php/')) {
            testEndpoint = '../api/test.php';
        } else {
            testEndpoint = 'api/test.php';
        }
        
        console.log('Testing basic API connectivity with:', testEndpoint);
        
        try {
            const testResponse = await safeApiRequest(testEndpoint);
            console.log('Basic API connectivity test successful:', testResponse);
        } catch (testError) {
            console.error('Basic API connectivity test failed:', testError);
            showError('Cannot connect to the API. Switching to local storage mode.');
            
            // Activate local storage fallback
            LocalStorageFallback.initialize();
            return setupPrayerTrackingUI(userId); // Skip database test and just set up UI
        }
        
        // Now test database connectivity
        const dbTestUrl = `${API_ENDPOINTS.prayer_tracking}?action=test_db`;
        console.log('Testing database connectivity with:', dbTestUrl);
        
        const dbResponse = await safeApiRequest(dbTestUrl);
        console.log('Database connectivity test:', dbResponse);
        
        if (dbResponse.code !== 200) {
            throw new Error(`Database test failed: ${dbResponse.message}`);
        }
    } catch (error) {
        console.error('API connectivity verification failed:', error);
        showError(`API error: ${error.message}. Switching to local storage mode.`);
        
        // Activate local storage fallback
        LocalStorageFallback.initialize();
        
        // Show warning about local storage mode
        const container = document.querySelector('.container');
        if (container) {
            const warningAlert = document.createElement('div');
            warningAlert.className = 'alert alert-warning alert-dismissible fade show mt-3';
            warningAlert.innerHTML = `
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Local Storage Mode Active</h5>
                <p>Unable to connect to database: ${error.message}</p>
                <hr>
                <p class="mb-0">Prayer tracking is now using your browser's local storage. Changes will only be saved on this device and may be lost if you clear browser data.</p>
                <p>Visit the <a href="api/setup_db.php" target="_blank">Database Setup Page</a> to resolve this issue.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            container.insertBefore(warningAlert, container.firstChild);
        }
    }
    
    // Set up the prayer tracking UI
    setupPrayerTrackingUI(userId);
}

// Function to set up the prayer tracking UI
function setupPrayerTrackingUI(userId) {
    // Check if we have the necessary DOM elements
    const prayerCards = document.querySelectorAll('.prayer-card');
    console.log('Found prayer cards:', prayerCards.length);
    
    // Initialize prayer buttons
    prayerCards.forEach(card => {
        const prayerId = card.dataset.prayerId;
        const pendingBtn = card.querySelector('.btn-pending');
        const completedBtn = card.querySelector('.btn-completed');
        
        console.log('Setting up card for prayer:', prayerId, 'Buttons:', !!pendingBtn, !!completedBtn);
        
        if (pendingBtn && completedBtn) {
            pendingBtn.addEventListener('click', async () => {
                console.log('Pending button clicked for:', prayerId);
                try {
                    await updatePrayerStatus(userId, prayerId, 'pending');
                    await updateAllStats(userId);
                } catch (error) {
                    console.error('Error updating prayer status:', error);
                    // Still update UI even if API fails
                    updatePrayerCardUI(prayerId, 'pending');
                }
            });
            
            completedBtn.addEventListener('click', async () => {
                console.log('Completed button clicked for:', prayerId);
                try {
                    await updatePrayerStatus(userId, prayerId, 'completed');
                    await updateAllStats(userId);
                } catch (error) {
                    console.error('Error updating prayer status:', error);
                    // Still update UI even if API fails
                    updatePrayerCardUI(prayerId, 'completed');
                }
            });
        }
    });
    
    // Load initial prayer statuses
    loadPrayerStatus(userId).catch(error => {
        console.error('Failed to load prayer statuses:', error);
        // Continue without initial status - defaults to pending
    });
    
    // Update all statistics
    updateAllStats(userId).catch(error => {
        console.error('Failed to update statistics:', error);
        // Continue without statistics
    });
}

async function updatePrayerStatus(userId, prayerId, status) {
    try {
        console.log(`Updating prayer status: User: ${userId}, Prayer: ${prayerId}, Status: ${status}`);
        
        const data = {
            user_id: userId,
            prayer_id: prayerId,
            status: status,
            date: new Date().toISOString().split('T')[0]
        };
        
        console.log('Sending data:', data);
        
        let result;
        
        // Check if we're using local storage fallback
        if (LocalStorageFallback.isActive) {
            console.log('Using local storage fallback for updating prayer status');
            result = LocalStorageFallback.savePrayer(data);
        } else {
            // Use the safe API request helper
            result = await safeApiRequest(API_ENDPOINTS.prayer_tracking, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
        }
        
        console.log('Update prayer status response:', result);
        
        // Update the UI
        updatePrayerCardUI(prayerId, status);
        
        // Also update the statistics
        await Promise.all([
            updateWeeklyStats(userId),
            updateMonthlyStats(userId)
        ]);
        
        return result;
    } catch (error) {
        console.error('Error updating prayer status:', error);
        showError('Failed to update prayer status: ' + error.message);
        throw error;
    }
}

function updatePrayerCardUI(prayerId, status) {
    const card = document.querySelector(`.prayer-card[data-prayer-id="${prayerId}"]`);
    if (!card) return;
    
    const pendingBtn = card.querySelector('.btn-pending');
    const completedBtn = card.querySelector('.btn-completed');
    
    if (pendingBtn && completedBtn) {
        if (status === 'completed') {
            pendingBtn.classList.remove('active');
            completedBtn.classList.add('active');
        } else {
            pendingBtn.classList.add('active');
            completedBtn.classList.remove('active');
        }
    }
}

async function loadPrayerStatus(userId) {
    try {
        console.log('Loading prayer statuses for user:', userId);
        const today = new Date().toISOString().split('T')[0];
        
        let result;
        
        // Check if we're using local storage fallback
        if (LocalStorageFallback.isActive) {
            console.log('Using local storage fallback for loading prayer statuses');
            result = LocalStorageFallback.getPrayers(userId, today, today, true);
        } else {
            const apiUrl = `${API_ENDPOINTS.prayer_tracking}?user_id=${userId}&start_date=${today}&end_date=${today}&detail=true`;
            console.log('Fetching prayer status from:', apiUrl);
            
            // Use the safe API request helper
            result = await safeApiRequest(apiUrl);
        }
        
        console.log('Prayer statuses loading response:', result);
        
        // First reset all buttons to pending state
        const allPrayerCards = document.querySelectorAll('.prayer-card');
        allPrayerCards.forEach(card => {
            const pendingBtn = card.querySelector('.btn-pending');
            const completedBtn = card.querySelector('.btn-completed');
            if (pendingBtn && completedBtn) {
                pendingBtn.classList.add('active');
                completedBtn.classList.remove('active');
            }
        });
        
        // Then update buttons based on fetched data
        if (result.code === 200 && result.data && result.data.prayers) {
            console.log('Found', result.data.prayers.length, 'prayer records for today');
            // Update UI based on returned prayers
            result.data.prayers.forEach(prayer => {
                console.log('Updating UI for prayer:', prayer.prayer_id, 'status:', prayer.status);
                updatePrayerCardUI(prayer.prayer_id, prayer.status);
            });
        } else {
            console.log('No prayer records found or invalid response format');
            // All buttons are already reset to pending by default from our code above
        }
    } catch (error) {
        console.error('Error loading prayer statuses:', error);
        showError('Failed to load prayer statuses: ' + error.message);
    }
}

async function updateAllStats(userId) {
    console.log('Updating all stats for user:', userId);
    
    try {
        const dailyStats = await updateDailyStats(userId);
        console.log('Daily stats updated:', dailyStats);
        
        const weeklyStats = await updateWeeklyStats(userId);
        console.log('Weekly stats updated:', weeklyStats);
        
        const monthlyStats = await updateMonthlyStats(userId);
        console.log('Monthly stats updated:', monthlyStats);
        
        return { daily: dailyStats, weekly: weeklyStats, monthly: monthlyStats };
    } catch (error) {
        console.error('Error updating all stats:', error);
        showError('Failed to update prayer statistics. Please refresh the page.');
    }
}

async function getDayStats(userId, date) {
    try {
        console.log('Getting day stats for:', date);
        const apiUrl = `${API_ENDPOINTS.prayer_tracking}?user_id=${userId}&start_date=${date}&end_date=${date}`;
        console.log('Day stats API URL:', apiUrl);
        
        const response = await fetch(apiUrl);
        
        if (!response.ok) {
            // Try to get the error response as text first
            const errorText = await response.text();
            console.error('Error response text:', errorText);
            throw new Error(`Failed to get day stats: ${response.status} ${response.statusText}`);
        }

        let result;
        try {
            result = await response.json();
        } catch (jsonError) {
            console.error('JSON parse error:', jsonError);
            throw new Error('Invalid JSON response from server');
        }
        
        console.log('Day stats API response:', result);
        
        if (result.code === 200 && result.data) {
            const stats = result.data;
            return {
                total: 5, // 5 daily prayers
                completed: stats.completed || 0
            };
        } else {
            throw new Error('Invalid response format from API');
        }
    } catch (error) {
        console.error('Error getting day stats:', error);
        return { total: 5, completed: 0, error: error.message };
    }
}

async function updateDailyStats(userId) {
    const dailyStats = document.getElementById('daily-stats');
    if (!dailyStats) return;

    // Show loading indicator
    dailyStats.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading daily stats...</div>';

    try {
        const today = new Date().toISOString().split('T')[0];
        const stats = await getDayStats(userId, today);
        
        if (stats.error) {
            throw new Error(stats.error);
        }
        
        const percentage = Math.round((stats.completed / stats.total) * 100);

        console.log('Daily stats:', stats, 'Percentage:', percentage);
        dailyStats.innerHTML = generateStatsHTML('Today', stats, percentage);
        return stats;
    } catch (error) {
        console.error('Error updating daily stats:', error);
        dailyStats.innerHTML = '<div class="alert alert-danger">Stats unavailable: ' + error.message + '</div>';
    }
}

async function updateWeeklyStats(userId) {
    try {
        console.log('Updating weekly stats for user:', userId);
        
        // Get today and 6 days ago
        const today = new Date();
        const endDate = today.toISOString().split('T')[0];
        
        // Calculate start date (6 days ago)
        const startDate = new Date(today);
        startDate.setDate(today.getDate() - 6);
        const startDateStr = startDate.toISOString().split('T')[0];
        
        console.log(`Weekly range: ${startDateStr} to ${endDate}`);
        
        // Total weekly prayers is always 35 (7 days * 5 prayers per day)
        const totalWeeklyPrayers = 35;
        
        let result;
        
        // Check if we're using local storage fallback
        if (LocalStorageFallback.isActive) {
            console.log('Using local storage fallback for weekly stats');
            result = LocalStorageFallback.getPrayers(userId, startDateStr, endDate, false);
        } else {
            // Use safe API request helper
            const apiUrl = `${API_ENDPOINTS.prayer_tracking}?user_id=${userId}&start_date=${startDateStr}&end_date=${endDate}`;
            console.log('Fetching weekly stats from:', apiUrl);
            
            result = await safeApiRequest(apiUrl);
        }
        
        console.log('Weekly stats API response:', result);
        
        if (result.code === 200 && result.data) {
            const completedPrayers = result.data.completed || 0;
            
            // Calculate percentage based on total weekly prayers (35)
            // For example, if 8 prayers are completed:
            // Percentage = (8/35) * 100 = 22.86%
            const progressPercentage = Math.round((completedPrayers / totalWeeklyPrayers) * 100);
            
            const stats = {
                completed: completedPrayers,
                total: totalWeeklyPrayers, // Always show the full week total (7 days * 5 prayers)
                percentage: progressPercentage,
                context: 'week' // Add context to indicate this is weekly stats
            };
            
            console.log('Calculated weekly stats:', stats);
            
            // Update the weekly stats display
            const weeklyStatsContainer = document.getElementById('weekly-stats');
            if (weeklyStatsContainer) {
                weeklyStatsContainer.innerHTML = generateStatsHTML(stats, 'Weekly Progress');
            } else {
                console.error('Weekly stats container not found');
            }
            
            return stats;
        } else {
            console.error('Invalid weekly stats response format:', result);
            throw new Error('Invalid weekly stats response format');
        }
    } catch (error) {
        console.error('Error updating weekly stats:', error);
        showError('Failed to update weekly statistics: ' + error.message);
    }
}

async function updateMonthlyStats(userId) {
    try {
        console.log('Updating monthly stats for user:', userId);
        
        // Get current month details
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth(); // 0-11
        
        // Calculate number of days in current month
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Calculate start and end dates for the current month
        const startDate = `${year}-${(month + 1).toString().padStart(2, '0')}-01`;
        const endDate = today.toISOString().split('T')[0];
        
        console.log(`Monthly range: ${startDate} to ${endDate}, Days in month: ${daysInMonth}`);
        
        // Calculate days passed in the month so far
        const dayOfMonth = today.getDate();
        // Expected prayers based on days passed (5 prayers per day)
        const expectedPrayers = dayOfMonth * 5;
        
        console.log(`Day of month: ${dayOfMonth}, Expected prayers: ${expectedPrayers}`);
        
        let result;
        
        // Check if we're using local storage fallback
        if (LocalStorageFallback.isActive) {
            console.log('Using local storage fallback for monthly stats');
            result = LocalStorageFallback.getPrayers(userId, startDate, endDate, false);
        } else {
            // Use safe API request helper
            const apiUrl = `${API_ENDPOINTS.prayer_tracking}?user_id=${userId}&start_date=${startDate}&end_date=${endDate}`;
            console.log('Fetching monthly stats from:', apiUrl);
            
            result = await safeApiRequest(apiUrl);
        }
        
        console.log('Monthly stats API response:', result);
        
        if (result.code === 200 && result.data) {
            const completedPrayers = result.data.completed || 0;
            const stats = {
                completed: completedPrayers,
                total: expectedPrayers,
                totalMonth: daysInMonth * 5, // total prayers for the full month
                percentage: Math.round((completedPrayers / expectedPrayers) * 100)
            };
            
            console.log('Calculated monthly stats:', stats);
            
            // Update the monthly stats display
            const monthlyStatsContainer = document.getElementById('monthly-stats');
            if (monthlyStatsContainer) {
                monthlyStatsContainer.innerHTML = generateStatsHTML(stats, 'Monthly Progress');
            } else {
                console.error('Monthly stats container not found');
            }
            
            return stats;
        } else {
            console.error('Invalid monthly stats response format:', result);
            throw new Error('Invalid monthly stats response format');
        }
    } catch (error) {
        console.error('Error updating monthly stats:', error);
        showError('Failed to update monthly statistics: ' + error.message);
    }
}

function generateStatsHTML(stats, title) {
    // Check if stats is passed as separate arguments (daily stats format)
    if (typeof stats === 'string' && arguments.length >= 3) {
        const period = stats;
        const statsData = arguments[1];
        const percentage = arguments[2];
        
        return generateStatsContainer(percentage, statsData.completed, statsData.total);
    }
    
    // New format with stats object and title
    const percentage = stats.percentage || 0;
    const completed = stats.completed || 0;
    const total = stats.total || 0;
    
    // Build additional details about completed vs total prayers
    let context = '';
    
    // Add weekly or monthly context if available
    // if (stats.totalWeek) {
    //     context = `(out of ${stats.totalWeek} for the full week)`;
    // } else if (stats.totalMonth) {
    //     context = `(out of ${stats.totalMonth} for the full month)`;
    // }
    
    return generateStatsContainer(percentage, completed, total, title, context);
}

function generateStatsContainer(percentage, completed, total, title = null, context = '') {
    // Color based on percentage
    let progressColor = getProgressColor(percentage);
    
    // Create details HTML
    let detailsHTML;
    
    // For weekly stats, always show the total for the full week (35 prayers)
    if (context && context.includes('week')) {
        detailsHTML = `<p class="mb-1">Completed: ${completed} of 35 prayers</p>`;
    } else if (context) {
        // For other stats with context, use the provided format
        detailsHTML = `<p class="mb-1">Completed: ${completed} of ${total} prayers ${context}</p>`;
    } else {
        // Default format for daily stats
        detailsHTML = `<p class="mb-1">Completed: ${completed} of ${total} prayers</p>`;
    }

    // Cap the width of the progress bar at 100% for visual display
    // but show the actual percentage in the text
    const barWidth = Math.min(100, percentage);
    
    return `
        <div class="stats-container">
            ${title ? `<h5 class="card-title mb-3">${title}</h5>` : ''}
            <div class="text-center mb-3">
                <div class="progress" style="height: 25px; position: relative;">
                    <div class="progress-bar ${progressColor}" role="progressbar" 
                         style="width: ${barWidth}%;" 
                         aria-valuenow="${percentage}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                    <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; color: #333">
                        ${percentage}%
                    </span>
                </div>
            </div>
            <div class="stats-details">
                ${detailsHTML}
            </div>
        </div>
    `;
}

function getProgressColor(percentage) {
    if (percentage >= 80) return 'bg-success';
    if (percentage >= 60) return 'bg-info';
    if (percentage >= 40) return 'bg-warning';
    return 'bg-danger';
}

// Utility Functions

/**
 * Shows an error message to the user
 * @param {string} message - The error message to display
 * @param {number} duration - How long to show the message in milliseconds (default: 5000ms)
 */
function showError(message, duration = 5000) {
    // Check if an error container already exists
    let errorContainer = document.getElementById('error-notification');
    
    // If not, create one
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'error-notification';
        errorContainer.className = 'error-notification';
        document.body.appendChild(errorContainer);
    }
    
    // Create the error message element
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.innerHTML = `
        <div class="error-content">
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        </div>
        <button class="close-error"><i class="fas fa-times"></i></button>
    `;
    
    // Add click handler to close button
    errorElement.querySelector('.close-error').addEventListener('click', () => {
        errorElement.classList.add('fade-out');
        setTimeout(() => {
            if (errorElement.parentNode) {
                errorElement.parentNode.removeChild(errorElement);
            }
        }, 300);
    });
    
    // Add the error to the container
    errorContainer.appendChild(errorElement);
    
    // Auto-remove after duration
    setTimeout(() => {
        if (errorElement.parentNode) {
            errorElement.classList.add('fade-out');
            setTimeout(() => {
                if (errorElement.parentNode) {
                    errorElement.parentNode.removeChild(errorElement);
                }
            }, 300);
        }
    }, duration);
}

function showPrayerTimesError(message) {
    console.error(message);
    const prayers = ['imsak', 'fajr', 'sunrise', 'dhuhr', 'asr', 'maghrib', 'isha', 'midnight'];
    prayers.forEach(prayer => {
        const element = document.getElementById(prayer);
        if (element) {
            element.textContent = '--:--';
        }
    });

    const locationElement = document.getElementById('location');
    if (locationElement) {
        locationElement.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${message}
            </div>
        `;
    }
}

// Local storage fallback for when API is unavailable
const LocalStorageFallback = {
    isActive: false,
    
    initialize: function() {
        this.isActive = true;
        console.log('LocalStorage fallback activated');
        
        // Create prayers collection if it doesn't exist
        if (!localStorage.getItem('prayers')) {
            localStorage.setItem('prayers', JSON.stringify([]));
        }
    },
    
    getPrayers: function(userId, startDate, endDate, detail = false) {
        try {
            const prayers = JSON.parse(localStorage.getItem('prayers') || '[]');
            const filteredPrayers = prayers.filter(prayer => {
                return prayer.user_id === userId && 
                       prayer.date >= startDate && 
                       prayer.date <= endDate;
            });
            
            if (detail) {
                return {
                    code: 200,
                    message: 'Prayer details retrieved from local storage',
                    data: {
                        prayers: filteredPrayers
                    }
                };
            } else {
                const completed = filteredPrayers.filter(prayer => prayer.status === 'completed').length;
                return {
                    code: 200,
                    message: 'Prayer stats retrieved from local storage',
                    data: {
                        completed: completed
                    }
                };
            }
        } catch (error) {
            console.error('Error accessing local storage:', error);
            return {
                code: 500,
                message: 'Error accessing local storage',
                data: detail ? { prayers: [] } : { completed: 0 }
            };
        }
    },
    
    savePrayer: function(prayerData) {
        try {
            const prayers = JSON.parse(localStorage.getItem('prayers') || '[]');
            
            // Check if this prayer already exists
            const existingIndex = prayers.findIndex(prayer => 
                prayer.user_id === prayerData.user_id && 
                prayer.prayer_id === prayerData.prayer_id && 
                prayer.date === prayerData.date
            );
            
            if (existingIndex >= 0) {
                // Update existing prayer
                prayers[existingIndex].status = prayerData.status;
                prayers[existingIndex].updated_at = new Date().toISOString();
                
                localStorage.setItem('prayers', JSON.stringify(prayers));
                
                return {
                    code: 200,
                    message: 'Prayer status updated in local storage',
                    data: {
                        id: existingIndex,
                        updated: true
                    }
                };
            } else {
                // Add new prayer
                const newPrayer = {
                    ...prayerData,
                    id: prayers.length + 1,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString()
                };
                
                prayers.push(newPrayer);
                localStorage.setItem('prayers', JSON.stringify(prayers));
                
                return {
                    code: 201,
                    message: 'Prayer status created in local storage',
                    data: {
                        id: newPrayer.id,
                        created: true
                    }
                };
            }
        } catch (error) {
            console.error('Error saving to local storage:', error);
            return {
                code: 500,
                message: 'Error saving to local storage',
                data: null
            };
        }
    }
};

// Date simulation for testing reset periods
function simulateDateForTesting(daysToAdd) {
    // Store the real Date constructor
    const RealDate = Date;
    
    // Get the current date to use as base for simulation
    const currentDate = new Date();
    
    // Calculate the simulated date by adding days
    const simulatedDate = new Date(currentDate);
    simulatedDate.setDate(simulatedDate.getDate() + daysToAdd);
    
    // Override the Date constructor globally
    window.Date = function(...args) {
        // When called without arguments, return our simulated date
        if (args.length === 0) {
            return new RealDate(simulatedDate);
        }
        // Otherwise use the real Date constructor
        return new RealDate(...args);
    };
    
    // Copy all methods and properties from the real Date to our fake Date
    Object.setPrototypeOf(window.Date, RealDate);
    window.Date.prototype = RealDate.prototype;
    window.Date.now = () => simulatedDate.getTime();
    
    // Return a function to restore the real Date
    return function restoreRealDate() {
        window.Date = RealDate;
        console.log("Real date restored");
        // Reload the page to refresh all date-dependent data
        window.location.reload();
    };
}

// Function to add testing controls to the UI
function addDateTestingControls() {
    // Only add controls if we're in the tracking page
    if (!document.querySelector('.prayer-cards-container')) return;
    
    let restoreFunction = null;
    
    const controlsContainer = document.createElement('div');
    controlsContainer.className = 'card mb-4';
    controlsContainer.innerHTML = `
        <div class="card-header bg-warning">
            <h4><i class="fas fa-calendar-alt me-2"></i>Date Testing Controls</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <p>Use these controls to simulate different dates and verify period resets.</p>
                <ul>
                    <li>Daily progress resets every day</li>
                    <li>Weekly progress resets at the end of each week</li>
                    <li>Monthly progress resets at the end of each month</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <button class="btn btn-primary btn-simulate" data-days="1">
                        <i class="fas fa-calendar-day me-2"></i>Next Day
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-primary btn-simulate" data-days="7">
                        <i class="fas fa-calendar-week me-2"></i>Next Week
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-primary btn-simulate" data-days="30">
                        <i class="fas fa-calendar-alt me-2"></i>Next Month
                    </button>
                </div>
            </div>
            <div class="mt-3" id="simulated-date-info"></div>
            <div class="mt-3">
                <button class="btn btn-danger d-none" id="restore-date">
                    <i class="fas fa-undo me-2"></i>Restore Real Date
                </button>
            </div>
        </div>
    `;
    
    // Insert the controls at the top of the container
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(controlsContainer, container.firstChild);
    }
    
    // Add event listeners to buttons
    controlsContainer.querySelectorAll('.btn-simulate').forEach(btn => {
        btn.addEventListener('click', () => {
            const days = parseInt(btn.dataset.days, 10);
            // If we already have a restore function, call it first
            if (restoreFunction) {
                restoreFunction();
            }
            
            // Simulate new date
            restoreFunction = simulateDateForTesting(days);
            
            // Update UI to show simulated date
            const dateInfo = document.getElementById('simulated-date-info');
            const simulatedDate = new Date();
            dateInfo.innerHTML = `
                <div class="alert alert-warning">
                    <strong>Simulating date:</strong> ${simulatedDate.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}
                </div>
            `;
            
            // Show the restore button
            document.getElementById('restore-date').classList.remove('d-none');
            
            // Reload prayer tracking data for the simulated date
            const userId = localStorage.getItem('user_id');
            if (userId) {
                loadPrayerStatus(userId);
                updateAllStats(userId);
            }
        });
    });
    
    // Add event listener to restore button
    document.getElementById('restore-date').addEventListener('click', () => {
        if (restoreFunction) {
            restoreFunction();
        }
    });
    
    console.log("Date testing controls added");
}

// Add the testing controls when the document is ready
document.addEventListener('DOMContentLoaded', () => {
    // Add other initialization code...
    
    // Only in development or if specifically enabled
    const enableTestControls = localStorage.getItem('enable_date_testing') === 'true' || 
                               window.location.search.includes('test_mode=true');
    
    if (enableTestControls) {
        addDateTestingControls();
    }
    
    // Allow enabling test controls with Ctrl+Shift+T
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && e.key === 'T') {
            localStorage.setItem('enable_date_testing', 'true');
            if (!document.querySelector('.btn-simulate')) {
                addDateTestingControls();
            }
        }
    });
});