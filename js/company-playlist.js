const videoPlayerOverlay = document.getElementById('video-player-overlay');
const closeButton = document.getElementById('close-button');
const mainVideo = document.getElementById('main-video');
const mainVideoTitle = document.getElementById('main-video-title');
const videoPlaylistContainer = document.getElementById('video-playlist');
const videoPlaylistTitle = document.getElementById('video-playlist-title');
// const playlistCard = document.querySelectorAll(".playlist-card");
// const prevBtn = document.getElementById('prev-btn'); // For left sliding
// const nextBtn = document.getElementById('next-btn'); // For right sliding

// Set the video player volume to maximum on load
mainVideo.volume = 1; // Set volume to 100%

// Close video player overlay when clicking outside the video player container
videoPlayerOverlay.addEventListener('click', function (event) {
    if (event.target === this) { // If clicked outside the video player container
        videoPlayerOverlay.classList.add('hidden');
        mainVideo.pause();
        mainVideo.src = ''; // Stop the video when closing the overlay
    }
});

// Function to toggle play/pause
function togglePlayPause() {
    if (mainVideo.paused) {
        mainVideo.play(); // Play the video if it's paused
    } else {
        mainVideo.pause(); // Pause the video if it's playing
    }
}

// Add event listeners for both click and touchstart to handle play/pause
mainVideo.addEventListener('click', togglePlayPause); // For mouse clicks
mainVideo.addEventListener('touchstart', (event) => {
    event.preventDefault(); // Prevent default touch behavior (e.g., scrolling)
    togglePlayPause(); // Call the same toggle function
});

// Load playlist videos
document.querySelectorAll('.playlist-card').forEach(card => {
    card.addEventListener('click', () => {
        const videos = JSON.parse(card.dataset.videos);
        const firstVideo = videos[0];

        // Ensure video player remains in the overlay container and doesn't go fullscreen
        mainVideo.src = `./files/video/${firstVideo.file}`;
        mainVideoTitle.textContent = firstVideo.title;

        // Get the title from the card
        const playlistTitle = card.querySelector(".playlist-title").textContent;

        // Fetch the title text of video playlist
        videoPlaylistTitle.textContent = `Playlist of ${playlistTitle}`;

        videoPlaylistContainer.innerHTML = ''; // Clear the previous playlist
        videos.forEach((video, index) => {
            const videoItem = document.createElement('div');
            videoItem.classList.add('video-item');
            
            // Create a video element to capture the first frame
            const videoElement = document.createElement('video');
            videoElement.src = `./files/video/${video.file}`;
            videoElement.preload = "metadata"; // Preload metadata to get the video duration and dimensions

            // Create a canvas to draw the first frame
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            // Wait for the video to load and capture the first frame
            videoElement.addEventListener('loadeddata', () => {
                videoElement.currentTime = 1.5; // Go to the first frame (you can adjust this time slightly)

                // Draw the first frame on the canvas
                videoElement.addEventListener('seeked', () => {
                    const width = 320; // Set a smaller width like YouTube thumbnail
                    const height = 180; // Adjust height to maintain aspect ratio
                    canvas.width = width;
                    canvas.height = height;
                    context.drawImage(videoElement, 0, 0, width, height);

                    // Create an image from the canvas and set it as the thumbnail
                    const thumbnailImage = new Image();
                    thumbnailImage.src = canvas.toDataURL();
                    videoItem.appendChild(thumbnailImage);
                });
            });

            videoItem.innerHTML = `
                <p>${index + 1}. ${video.title} - ${video.duration}</p>
            `;

            videoItem.addEventListener('click', () => {
                mainVideo.src = `./files/video/${video.file}`;
                mainVideoTitle.textContent = video.title;
                mainVideo.play(); // Start the video immediately
            });

            videoPlaylistContainer.appendChild(videoItem);
        });

        // Make sure the overlay is not hidden when a new video is selected
        if (videoPlayerOverlay.classList.contains('hidden')) {
            videoPlayerOverlay.classList.remove('hidden');
        }
    });
});

// // Add an event listener to hide the title when the video starts playing
// mainVideo.addEventListener('play', () => {
//     mainVideoTitle.style.display = 'none'; // Hide the title when the video plays
// });

// // Add event listeners to scroll the playlist horizontally
// prevBtn.addEventListener('click', () => {
//     videoPlaylistContainer.scrollBy({ left: -320, behavior: 'smooth' }); // Scroll left
// });

// nextBtn.addEventListener('click', () => {
//     videoPlaylistContainer.scrollBy({ left: 320, behavior: 'smooth' }); // Scroll right
// });
