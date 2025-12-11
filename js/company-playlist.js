function showToast(message, type = 'success') {
    let container = document.querySelector('.admin-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'admin-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `admin-toast ${type}`;
    toast.textContent = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.5s forwards';
        setTimeout(() => {
            if (toast.parentElement) toast.remove();
        }, 500);
    }, 3000);
}

const videoPlayerOverlay = document.getElementById('video-player-overlay');
const mainVideo = document.getElementById('main-video');
const mainVideoTitle = document.getElementById('main-video-title');
const videoPlaylistContainer = document.getElementById('video-playlist');
const videoPlaylistTitle = document.getElementById('video-playlist-title');
const player = new Plyr(mainVideo, {
    controls: ['play-large', 'play', 'progress', 'current-time', 'duration', 'mute', 'volume', 'fullscreen'],
    fullscreen: { enabled: false } 
});

player.volume = 1; 

function createThumbnailAndAppend(video, index, videoItem, player) {
    const videoElement = document.createElement('video');
    videoElement.src = `./files/video/${video.file_name}`; 
    videoElement.preload = "metadata"; 

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    videoItem.addEventListener('click', () => {
        player.source = {
            type: 'video',
            sources: [
                { src: `./files/video/${video.file_name}`, type: 'video/mp4' }
            ]
        };
        mainVideoTitle.textContent = video.title;
        player.play();
    });

    videoElement.addEventListener('loadeddata', () => {
        videoElement.currentTime = 2; 

        videoElement.addEventListener('seeked', () => {
            const width = 320; 
            const height = 180; 
            canvas.width = width;
            canvas.height = height;
            context.drawImage(videoElement, 0, 0, width, height);

            const thumbnailImage = new Image();
            thumbnailImage.src = canvas.toDataURL();
            videoItem.appendChild(thumbnailImage);

            const videoInfo = document.createElement('p');
            videoInfo.textContent = `${index + 1}. ${video.title} - ${video.duration}`; 
            videoItem.appendChild(videoInfo);
        }, { once: true });
    });

    if(videoElement.readyState >= 2) {
         videoElement.dispatchEvent(new Event('loadeddata'));
    }
}

async function loadPlaylistVideos(playlistId, playlistTitle) {
    try {
        const response = await fetch(`api/video_api.php?action=get_videos&playlist_id=${playlistId}`);
        const result = await response.json();

        if (result.status === 'success' && result.data.length > 0) {
            const videos = result.data;
            const firstVideo = videos[0];
            
            // Load the first video into Plyr
            player.source = {
                type: 'video',
                sources: [
                    { src: `./files/video/${firstVideo.file_name}`, type: 'video/mp4' }
                ]
            };
            mainVideoTitle.textContent = firstVideo.title;

            // Update the playlist title
            videoPlaylistTitle.textContent = `Playlist of ${playlistTitle}`;

            // Generate playlist thumbnails (sidebar)
            videoPlaylistContainer.innerHTML = ''; 
            videos.forEach((video, index) => {
                const videoItem = document.createElement('div');
                videoItem.classList.add('video-item');
                videoItem.setAttribute('data-video-id', video.id);
                createThumbnailAndAppend(video, index, videoItem, player);
                videoPlaylistContainer.appendChild(videoItem);
            });

            // Show the overlay
            if (videoPlayerOverlay.classList.contains('hidden')) {
                videoPlayerOverlay.classList.remove('hidden');
            }
            player.play();

        } else if (result.data.length === 0) {
             showToast("This playlist has no videos yet. Please add videos through the admin panel.", 'info');
        } else {
            showToast("Failed to load playlist videos: " + result.message, 'error');
        }

    } catch (error) {
        console.error("Error loading videos:", error);
        showToast("A system error occurred while loading videos.", 'error');
    }
}


// Event listener for playlist cards
document.querySelectorAll('.playlist-card').forEach(card => {
    card.addEventListener('click', (e) => {
        if (e.target.closest('.admin-card-buttons') || e.target.closest('.admin-button-icon')) {
             return; 
        }

        const playlistId = card.getAttribute('data-playlist-id');
        const playlistTitle = card.querySelector('.playlist-title').textContent;
        
        loadPlaylistVideos(playlistId, playlistTitle);
    });
});

videoPlayerOverlay.addEventListener('click', (event) => {
    if (event.target === videoPlayerOverlay) { 
        videoPlayerOverlay.classList.add('hidden');
        player.pause();
        player.src = null; 
    }
});

const togglePlayPause = () => {
    player.paused ? player.play() : player.pause();
};

mainVideo.addEventListener('click', togglePlayPause); 
mainVideo.addEventListener('touchstart', (event) => {
    event.preventDefault();
    togglePlayPause();
});

// CRUD PLAYLIST FOR ADMIN
document.addEventListener("DOMContentLoaded", () => {
    const playlistModal = document.getElementById("admin-playlist-modal");
    if (!playlistModal) return; 

    const btnAddPlaylist = document.getElementById("admin-add-playlist-btn");
    const btnsEditPlaylist = document.querySelectorAll(".admin-button-edit");
    const btnsDeletePlaylist = document.querySelectorAll(".admin-button-delete");
    const btnsManageVideo = document.querySelectorAll(".admin-button-manage");
    const playlistForm = document.getElementById("admin-playlist-form");
    const playlistIdInput = document.getElementById("playlist-id");
    const playlistImageInput = document.getElementById("playlist-image");
    
    const managerModal = document.getElementById("admin-video-manager-modal");
    const videoFormModal = document.getElementById("admin-video-form-modal");
    const managerModalTitle = document.getElementById("video-manager-title");
    const videoListContainer = document.getElementById("video-list-container");
    const btnAddVideo = document.getElementById("admin-add-video-btn");
    const videoForm = document.getElementById("admin-video-form");
    const videoFormTitle = document.getElementById("video-form-title");
    const videoFormIdInput = document.getElementById("video-form-id");
    const videoManagerPlaylistIdInput = document.getElementById("video-manager-playlist-id");
    const videoFileInput = document.getElementById("video-file");
    const durationInput = document.getElementById('video-duration'); 
    
    const closeVideoManagerBtn = document.getElementById("admin-video-manager-close-btn");
    const cancelVideoManagerBtn = document.getElementById("admin-video-manager-cancel-btn");
    const closeVideoFormBtn = document.getElementById("admin-video-form-close-btn");
    const cancelVideoFormBtn = document.getElementById("admin-video-form-cancel-btn");

    let currentVideosData = [];

    // HELPER FUNCTION TO FORMAT DURATION
    const formatDuration = (seconds) => {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);
        const hh = h.toString().padStart(2, '0');
        const mm = m.toString().padStart(2, '0');
        const ss = s.toString().padStart(2, '0');
        return h > 0 ? `${hh}:${mm}:${ss}` : `00:${mm}:${ss}`;
    };

    if (videoFileInput) {
        videoFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file && file.type.startsWith('video/')) {
                const videoElement = document.createElement('video');
                videoElement.preload = 'metadata';
                
                videoElement.onloadedmetadata = function() {
                    window.URL.revokeObjectURL(videoElement.src);
                    const formattedTime = formatDuration(videoElement.duration);
                    
                    if (durationInput) {
                        durationInput.value = formattedTime;
                        // Efek visual sukses
                        durationInput.style.transition = "background-color 0.3s";
                        durationInput.style.backgroundColor = "#74e7d4"; 
                        setTimeout(() => durationInput.style.backgroundColor = "#1d2538", 500); 
                    }
                }
                videoElement.src = URL.createObjectURL(file);
            }
        });
    }

    const openManagerModal = (playlistId, title) => {
        managerModalTitle.textContent = `Manage Videos for ${title}`;
        videoManagerPlaylistIdInput.value = playlistId; 
        managerModal.classList.add("active");
        fetchAndRenderVideos(playlistId);
    };
    const closeManagerModal = () => managerModal.classList.remove("active"); 

    const openVideoFormModal = (mode, playlistId, videoData = null) => {
        videoForm.reset();
        videoManagerPlaylistIdInput.value = playlistId;

        if (mode === 'edit' && videoData) {
            videoFormTitle.textContent = "Edit Video";
            videoFormIdInput.value = videoData.id;
            document.getElementById("video-title").value = videoData.title;
            document.getElementById("video-duration").value = videoData.duration;
            document.getElementById("video-order").value = videoData.sort_order;
            videoFileInput.removeAttribute('required');

            if (videoData.file_name) {
                const fileSmall = videoFileInput.nextElementSibling;
                if (fileSmall && fileSmall.tagName === 'SMALL') {
                    fileSmall.innerHTML = `Current file: <span style="color: #74e7d4; font-weight: 600;">${videoData.file_name}</span>`;
                }
            }
        } else {
            videoFormTitle.textContent = "Add New Video";
            videoFormIdInput.value = "";
            durationInput.value = ""; 
            videoFileInput.setAttribute('required', 'required');
        }

        videoFormModal.classList.add("active");
        managerModal.classList.remove("active"); 
    };
    
    const closeVideoFormModal = () => {
        videoFormModal.classList.remove("active");
        managerModal.classList.add("active"); 
    };

    const fetchAndRenderVideos = async (playlistId) => {
        try {
            const response = await fetch(`api/video_api.php?action=get_videos&playlist_id=${playlistId}`);
            const result = await response.json();

            if (result.status === 'success') {
                currentVideosData = result.data; 
                videoListContainer.innerHTML = ''; 

                if (currentVideosData.length === 0) {
                    videoListContainer.innerHTML = '<p style="color: white; text-align: center; margin-top: 20px;">This playlist has no videos.</p>';
                    return;
                }

                const table = document.createElement('table');
                table.style.width = '100%';
                table.style.color = 'white';
                table.innerHTML = `
                    <thead style="background-color: #1d2538;">
                        <tr>
                            <th style="padding: 10px; text-align: left;">#</th>
                            <th style="padding: 10px; text-align: left;">Title</th>
                            <th style="padding: 10px; text-align: left;">Duration</th>
                            <th style="padding: 10px; text-align: center;">Order</th>
                            <th style="padding: 10px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="video-list-body"></tbody>
                `;
                videoListContainer.appendChild(table);
                const tbody = document.getElementById('video-list-body');

                currentVideosData.forEach((video, index) => {
                    const row = tbody.insertRow();
                    row.style.backgroundColor = index % 2 === 0 ? '#344054' : '#475467';
                    row.innerHTML = `
                        <td style="padding: 10px;">${index + 1}</td>
                        <td style="padding: 10px;">${video.title}</td>
                        <td style="padding: 10px;">${video.duration}</td>
                        <td style="padding: 10px; text-align: center;">${video.sort_order}</td>
                        <td style="padding: 10px; text-align: center; display: flex; justify-content: center; gap: 5px;">
                            <button class="admin-button-icon admin-button-edit-video" data-id="${video.id}" data-playlist-id="${video.playlist_id}" style="background-color: #ffc107; color: #333;"><i class="fas fa-edit"></i></button>
                            <button class="admin-button-icon admin-button-delete-video" data-id="${video.id}" style="background-color: #dc3545; color: white;"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    `;
                });

                document.querySelectorAll('.admin-button-edit-video').forEach(btn => btn.addEventListener('click', handleEditVideoClick));
                document.querySelectorAll('.admin-button-delete-video').forEach(btn => btn.addEventListener('click', handleDeleteVideoClick));

            } else {
                videoListContainer.innerHTML = `<p style="color: red; text-align: center; margin-top: 20px;">${result.message}</p>`;
            }
        } catch (error) {
            console.error("Error fetching videos:", error);
            videoListContainer.innerHTML = '<p style="color: red; text-align: center; margin-top: 20px;">Failed to connect to Video API.</p>';
        }
    };

    const handleEditVideoClick = (e) => {
        const videoId = e.currentTarget.getAttribute("data-id");
        const playlistId = e.currentTarget.getAttribute("data-playlist-id");
        const videoDetail = currentVideosData.find(v => v.id == videoId);
        if (videoDetail) {
            openVideoFormModal('edit', playlistId, videoDetail);
        } else {
            showToast("Video details not found.", 'error');
        }
    };

    const handleDeleteVideoClick = async (e) => {
        const videoId = e.currentTarget.getAttribute("data-id");
        if (!confirm("Are you sure you want to delete this video?")) return;

        const formData = new FormData();
        formData.append('id', videoId);

        try {
            const response = await fetch('api/video_api.php?action=delete_video', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                showToast(result.message, 'success');
                fetchAndRenderVideos(videoManagerPlaylistIdInput.value); 
            } else {
                showToast("Failed to delete: " + result.message, 'error');
            }
        } catch (error) {
            console.error("Error:", error);
            showToast("System error occurred while deleting video.", 'error');
        }
    };

    // EVENT LISTENERS PLAYLIST VIDEO MANAGEMENT
    btnsManageVideo.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const playlistId = btn.getAttribute("data-id");
            const playlistTitle = btn.closest('.admin-card-container').querySelector('.playlist-title').textContent;
            openManagerModal(playlistId, playlistTitle);
        });
    });

    if (btnAddVideo) {
        btnAddVideo.addEventListener("click", () => {
            openVideoFormModal('add', videoManagerPlaylistIdInput.value);
        });
    }

    if (closeVideoManagerBtn) closeVideoManagerBtn.addEventListener("click", closeManagerModal);
    if (cancelVideoManagerBtn) cancelVideoManagerBtn.addEventListener("click", closeManagerModal);
    if (closeVideoFormBtn) closeVideoFormBtn.addEventListener("click", closeVideoFormModal);
    if (cancelVideoFormBtn) cancelVideoFormBtn.addEventListener("click", closeVideoFormModal);


    if (videoForm) {
        videoForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(videoForm);
            const videoId = videoFormIdInput.value;
            const playlistId = videoManagerPlaylistIdInput.value;
            let action = videoId ? 'update_video' : 'add_video';
            let apiUrl = `api/video_api.php?action=${action}`;
            formData.append('playlist_id', playlistId); 

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    closeVideoFormModal();
                    fetchAndRenderVideos(playlistId); 
                } else {
                    showToast("Failed: " + result.message, 'error');
                }
            } catch (error) {
                console.error("Error:", error);
                showToast("System error occurred.", 'error');
            }
        });
    }
    
    const resetFormPlaylist = () => {
        playlistForm.reset();
        playlistIdInput.value = "";
    };

    const openPlaylistModal = (mode, data = null) => {
        resetFormPlaylist();
        if (mode === 'edit' && data) {
            document.getElementById("admin-playlist-modal-title").textContent = "Edit Playlist";
            playlistIdInput.value = data.id;
            document.getElementById("playlist-title").value = data.title;
            document.getElementById("playlist-order").value = data.sort_order;
            playlistImageInput.removeAttribute('required');

            if (data.thumbnail_image) {
                const imgSmall = playlistImageInput.nextElementSibling;
                if (imgSmall && imgSmall.tagName === 'SMALL') {
                    imgSmall.innerHTML = `Current file: <span style="color: #74e7d4; font-weight: 600;">${data.thumbnail_image}</span>`;
                }
            }
        } else {
            document.getElementById("admin-playlist-modal-title").textContent = "Add New Playlist";
            playlistImageInput.setAttribute('required', 'required');
        }
        playlistModal.classList.add("active");
    };

    const closePlaylistModal = () => {
        playlistModal.classList.remove("active");
    };

    if (btnAddPlaylist) {
        btnAddPlaylist.addEventListener("click", () => {
            openPlaylistModal('add');
        });
    }

    const plClose = document.getElementById("admin-playlist-modal-close-btn");
    const plCancel = document.getElementById("admin-playlist-modal-cancel-btn");
    if (plClose) plClose.addEventListener("click", closePlaylistModal);
    if (plCancel) plCancel.addEventListener("click", closePlaylistModal);

    btnsEditPlaylist.forEach(btn => {
        btn.addEventListener("click", async (e) => {
            e.stopPropagation();
            const playlistId = btn.getAttribute("data-id");
            try {
                const response = await fetch(`api/video_api.php?action=get_one&id=${playlistId}`);
                const result = await response.json();
                if (result.status === 'success') {
                    openPlaylistModal('edit', result.data); 
                } else {
                    showToast("Failed to fetch playlist data: " + result.message, 'error');
                }
            } catch (error) {
                console.error("Error fetching playlist:", error);
                showToast("System error occurred.", 'error');
            }
        });
    });

    btnsDeletePlaylist.forEach(btn => {
        btn.addEventListener("click", async (e) => {
            e.stopPropagation();
            if (!confirm("Are you sure you want to delete this playlist?")) return;
            const playlistId = btn.getAttribute("data-id");
            const formData = new FormData();
            formData.append('id', playlistId);
            try {
                const response = await fetch('api/video_api.php?action=delete', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast("Failed to delete: " + result.message, 'error');
                }
            } catch (error) {
                console.error("Error:", error);
                showToast("System error occurred.", 'error');
            }
        });
    });

    if (playlistForm) {
        playlistForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(playlistForm);
            const playlistId = playlistIdInput.value;
            let action = playlistId ? 'update' : 'add';
            let apiUrl = `api/video_api.php?action=${action}`;
            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    closePlaylistModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast("Failed: " + result.message, 'error');
                }
            } catch (error) {
                console.error("Error:", error);
                showToast("System error occurred.", 'error');
            }
        });
    }
});