// Valid language URLs - fetched dynamically from endpoint
let validLanguageUrls = []

async function fetchValidLanguageUrls() {
  try {
    const baseUrl = window.location.origin
    const response = await fetch(`${baseUrl}/endpoints/all-languages/`)
    const data = await response.json()
    validLanguageUrls = data.map((lang) => `/${lang.slug}/`)
    console.log("✅ Fetched valid language URLs:", validLanguageUrls.length, "languages")
  } catch (error) {
    console.error("❌ Failed to fetch language URLs:", error)
  }
}

// Import Swal library
const Swal = window.Swal || null

// Milestone definitions
const MILESTONES = {
  books: [
    { count: 1, title: "Hatchling", next: 3 },
    { count: 3, title: "Owlet", next: 5 },
    { count: 5, title: "Flapper", next: 10 },
    { count: 10, title: "Soarer", next: 20 },
    { count: 20, title: "Sky Flyer", next: 30 },
    { count: 30, title: "Hunter", next: 50 },
    { count: 50, title: "Hootmaster", next: 75 },
    { count: 75, title: "Sky Hero", next: 100 },
    { count: 100, title: "Wise Owl", next: 150 },
    { count: 150, title: "Starwing", next: null },
  ],
  activities: [
    { count: 1, title: "Hatchling", next: 3 },
    { count: 3, title: "Owlet", next: 5 },
    { count: 5, title: "Flapper", next: 10 },
    { count: 10, title: "Soarer", next: 15 },
    { count: 15, title: "Sky Flyer", next: 20 },
    { count: 20, title: "Hunter", next: 30 },
    { count: 30, title: "Hootmaster", next: 40 },
    { count: 40, title: "Sky Hero", next: 50 },
    { count: 50, title: "Wise Owl", next: 75 },
    { count: 75, title: "Starwing", next: null },
  ],
  languages: [
    { count: 1, title: "Hatchling", next: 3 },
    { count: 3, title: "Owlet", next: 5 },
    { count: 5, title: "Flapper", next: 7 },
    { count: 7, title: "Soarer", next: 10 },
    { count: 10, title: "Sky Flyer", next: 12 },
    { count: 12, title: "Hunter", next: 15 },
    { count: 15, title: "Hootmaster", next: 18 },
    { count: 18, title: "Sky Hero", next: 20 },
    { count: 20, title: "Wise Owl", next: 25 },
    { count: 25, title: "Starwing", next: null },
  ],
}

// Streak category configurations
const STREAK_CONFIGS = {
  app: {
    color: "#4994EC",
    categoryName: "App Usage Streak",
    actionText: "Use the app every day!",
    itemType: "days",
    imageUrl: "https://staging32.lote4kids.com/wp-content/uploads/2025/11/app-streak.png",
  },
  books: {
    color: "#67AD5B",
    categoryName: "Books Streak",
    actionText: "Read",
    itemType: "books",
    imageUrl: "https://staging32.lote4kids.com/wp-content/uploads/2025/11/book-streak.png",
  },
  activities: {
    color: "#8F31AA",
    categoryName: "Activities Streak",
    actionText: "Explore",
    itemType: "activities",
    imageUrl: "https://staging32.lote4kids.com/wp-content/uploads/2025/11/activities.png",
  },
  languages: {
    color: "#EE9B37",
    categoryName: "Languages Explored",
    actionText: "Explore",
    itemType: "languages",
    imageUrl: "https://staging32.lote4kids.com/wp-content/uploads/2025/11/languages-explored.png",
  },
}

// Helper function to get written ordinal number
function getWrittenOrdinal(num) {
  const ordinals = [
    "",
    "first",
    "second",
    "third",
    "fourth",
    "fifth",
    "sixth",
    "seventh",
    "eighth",
    "ninth",
    "tenth",
    "eleventh",
    "twelfth",
    "thirteenth",
    "fourteenth",
    "fifteenth",
    "sixteenth",
    "seventeenth",
    "eighteenth",
    "nineteenth",
    "twentieth",
    "twenty-first",
    "twenty-second",
    "twenty-third",
    "twenty-fourth",
    "twenty-fifth",
    "twenty-sixth",
    "twenty-seventh",
    "twenty-eighth",
    "twenty-ninth",
    "thirtieth",
  ]

  if (num <= 30) {
    return ordinals[num]
  }

  // For numbers beyond 30, fallback to numeric with suffix
  const suffixes = ["th", "st", "nd", "rd"]
  const v = num % 100
  return num + (suffixes[(v - 20) % 10] || suffixes[v] || suffixes[0])
}

// Helper function to get milestone position
function getMilestonePosition(milestones, count) {
  const sortedMilestones = milestones.sort((a, b) => a.count - b.count)
  const milestoneIndex = sortedMilestones.findIndex((m) => m.count === count)
  return milestoneIndex >= 0 ? milestoneIndex + 1 : 0
}

console.log("🎯 Activities & Books Script loaded on:", window.location.href)

if (typeof window.phpData === "undefined") {
  console.warn("⚠️ phpData not available, activities & books tracking disabled")
} else {
  console.log("✅ phpData available:", window.phpData)

  // Debug: Check all phpData properties
  console.log("🔍 Debug phpData properties:")
  console.log("- ownerIdFromPHP:", window.phpData.ownerIdFromPHP)
  console.log("- ajaxUrl:", window.phpData.ajaxUrl)
  console.log("- languageCount:", window.phpData.languageCount)

  async function startTracking() {
    await fetchValidLanguageUrls()
    initializeTracking()
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", startTracking)
  } else {
    startTracking()
  }
}

// Function to check if current page is a language page
function checkIfLanguagePage(pathname) {
  return validLanguageUrls.some((langUrl) => pathname.includes(langUrl))
}

// Function to extract language path from current URL
function extractLanguagePath(pathname) {
  for (const langUrl of validLanguageUrls) {
    if (pathname.includes(langUrl)) {
      console.log("🔍 Found matching language URL:", langUrl)
      return langUrl
    }
  }
  console.log("🔍 No matching language URL found for:", pathname)
  return null
}

function initializeTracking() {
  console.log("🚀 Initializing activity & book tracking...")

  // Debug: Log current URL and path
  console.log("🔍 Current URL:", window.location.href)
  console.log("🔍 Current pathname:", window.location.pathname)

  // Check for member home page
  const isMembersHomePage = window.location.pathname.includes("/member-home/")
  console.log("🏠 Is members home page:", isMembersHomePage)

  if (isMembersHomePage) {
    // Check if we have owner ID
    if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
      console.warn("⚠️ No owner ID available, cannot track members home visit")
    } else {
      console.log("✅ Valid members home page detected, logging daily visit...")
      logMembersHomeVisit()
    }
  }

  // Log language visit on page load (no longer click-based to avoid modal flash on site logo / nav clicks)
  const isLanguagePage = checkIfLanguagePage(window.location.pathname)
  if (isLanguagePage) {
    console.log("🌐 Language page detected, logging visit on page load...")
    if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
      console.warn("⚠️ No owner ID available, cannot log language visit")
    } else {
      logLanguageVisit()
    }
  }

  // Setup activity button tracking for Download and Watch buttons
  setupActivityButtonTracking()

  // Setup sidebar links activity streak tracking
  setupSidebarLinksTracking()

  // Check for activity pages
  const isActivityPage = window.location.pathname.includes("/activities/")
  console.log("📍 Current path:", window.location.pathname)
  console.log("🎯 Is activity page:", isActivityPage)

  if (isActivityPage) {
    const isJustActivitiesRoot = window.location.pathname.match(/\/activities\/?$/)
    if (isJustActivitiesRoot) {
      console.log("❌ Just /activities/ root page, skipping activity tracking")
    } else {
      // Check if we have owner ID
      if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
        console.warn("⚠️ No owner ID available, cannot track activity")
        return
      }

      console.log("✅ Valid activity page detected, logging visit...")
      logActivityVisit()
    }
  }

  // Check for book pages (aiovg_videos)
  const isBookPage = window.location.pathname.includes("/aiovg_videos/")
  console.log("📚 Is book page:", isBookPage)

  if (isBookPage) {
    const isJustBooksRoot = window.location.pathname.match(/\/aiovg_videos\/?$/)
    if (isJustBooksRoot) {
      console.log("❌ Just /aiovg_videos/ root page, skipping book tracking")
    } else {
      // Check if we have owner ID
      if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
        console.warn("⚠️ No owner ID available, cannot track book visit")
        return
      }

      console.log("✅ Valid book page detected, logging visit...")
      logBookVisit()
    }
  }

  setupIframePopupTracking()
}

function setupIframePopupTracking() {
  try {
    console.log("🔌 Setting up iframe popup tracking...")

    // Function to handle popup opening
    async function logIframePopupOpen() {
      try {
        console.log("📱 IFRAME POPUP DETECTED - Activity Logged!")

        // Check if we have owner ID
        if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
          console.warn("⚠️ No owner ID available, cannot track iframe popup")
          return
        }

        // Log the activity
        const formData = new FormData()
        formData.append("action", "log_activity_visit")
        formData.append("ownerId", window.phpData.ownerIdFromPHP)
        formData.append("currentUrl", window.location.href)

        const response = await fetch(window.phpData.ajaxUrl, {
          method: "POST",
          body: formData,
        })

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }

        const result = await response.json()
        console.log("📥 Iframe popup activity log response:", result)

        if (result.success) {
          console.log("✅ Iframe popup activity logged successfully!")

          if (window.phpData) {
            window.phpData.activityCount = result.newActivityCount
          }

          // if (typeof Swal !== "undefined") {
          //   Swal.fire({
          //     title: "✅ Activity Logged!",
          //     text: `Total activities: ${result.newActivityCount}`,
          //     icon: "success",
          //     timer: 2000,
          //     timerProgressBar: true,
          //     showConfirmButton: false,
          //     toast: true,
          //     position: "bottom-start",
          //   })
          // }

          // Dispatch custom event
          const activityLoggedEvent = new CustomEvent("iframePopupLogged", {
            detail: {
              newActivityCount: result.newActivityCount,
              message: result.message,
            },
          })
          document.dispatchEvent(activityLoggedEvent)

          // Check for milestone unlock
          const unlockedMilestone = MILESTONES.activities.find((m) => m.count === result.newActivityCount)
          if (unlockedMilestone) {
            const nextMilestone = MILESTONES.activities.find((m) => m.count > result.newActivityCount)
            const remaining = nextMilestone ? nextMilestone.count - result.newActivityCount : 0
            const config = STREAK_CONFIGS.activities
            const milestonePosition = getMilestonePosition(MILESTONES.activities, result.newActivityCount)
            const ordinalPosition = getWrittenOrdinal(milestonePosition)

            const htmlContent = `
              <div style="text-align: center; padding: 10px;">
                <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
                <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
                  <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
                  <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
                </div>
                <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
                <p style="margin-top:15px; font-size: 16px; color:#444;">
                  ${config.actionText} ${remaining > 0 ? `${remaining} more ${config.itemType} to earn your next book.` : "You have reached the final tier!"}
                </p>
              </div>
            `

            const resultSwal = await Swal.fire({
              html: `
                <style>
                  .swal2-cancel.swal-cancel-btn {
                    color: #0d6b58 !important;
                    box-shadow: none !important;
                    margin-top: -30px !important;
                    padding: 10px 25px !important;
                  }
                  .swal2-confirm.swal-confirm-btn {
                    color: #0d6b58 !important;
                    box-shadow: none !important;
                    margin-top: -30px !important;
                    padding: 10px 25px !important;
                  }
                </style>
                ${htmlContent}
              `,
              width: 450,
              background: "rgba(255, 255, 255, 0.8)",
              showConfirmButton: true,
              showCancelButton: true,
              confirmButtonText: "View My Shelf",
              cancelButtonText: "Okay",
              confirmButtonColor: "transparent",
              cancelButtonColor: "transparent",
              allowOutsideClick: true,
              didDismiss: (dismiss) => {
                console.log("Modal dismissed:", dismiss)
              },
              customClass: {
                popup: "book-unlock-modal",
                confirmButton: "swal-confirm-btn",
                cancelButton: "swal-cancel-btn",
              },
            })

            if (resultSwal.isConfirmed) {
              window.location.href = "/bookshelf/"
            }
          }
        }
      } catch (error) {
        console.error("❌ Error logging iframe popup activity:", error)
      }
    }

    // Setup MutationObserver to detect iframe popup appearance
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        // Check for newly added nodes
        if (mutation.addedNodes.length) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              // Element node
              // Check if this is the iframe popup or contains it
              if (node.classList && node.classList.contains("lfk-second-iframe-popup")) {
                if (node.classList.contains("active")) {
                  console.log("✨ Iframe popup appeared with .active class")
                  logIframePopupOpen()
                }
              }

              // Check for existing iframe popup in children
              const iframePopup = node.querySelector?.(".lfk-second-iframe-popup.active")
              if (iframePopup) {
                console.log("✨ Found iframe popup with .active in children")
                logIframePopupOpen()
              }
            }
          })
        }

        // Check for class changes on existing elements
        if (mutation.type === "attributes" && mutation.attributeName === "class") {
          const target = mutation.target
          if (
            target.classList &&
            target.classList.contains("lfk-second-iframe-popup") &&
            target.classList.contains("active")
          ) {
            console.log("✨ Iframe popup .active class added")
            logIframePopupOpen()
          }
        }
      })
    })

    // Start observing the document for changes
    observer.observe(document.body, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ["class"],
      attributeOldValue: true,
    })

    console.log("✅ Iframe popup tracking initialized with MutationObserver")

    // Also check if iframe popup already exists on page load
    const existingPopup = document.querySelector(".lfk-second-iframe-popup.active")
    if (existingPopup) {
      console.log("✨ Iframe popup already exists on page load - logging activity")
      logIframePopupOpen()
    }
  } catch (error) {
    console.error("❌ Error setting up iframe popup tracking:", error)
  }
}

// Setup activity button tracking for Download and Watch buttons
function setupActivityButtonTracking() {
  try {
    console.log("🔘 Setting up activity button tracking...")

    // Find Download button with id="download-activity"
    const downloadButton = document.querySelector("#download-activity")
    if (downloadButton && !downloadButton.dataset.activityHandlerAdded) {
      downloadButton.dataset.activityHandlerAdded = "true"

      downloadButton.addEventListener("click", (event) => {
        event.preventDefault()
        event.stopPropagation()

        // Prevent double-clicks
        if (downloadButton.dataset.clicked === "true") {
          console.log("🔘 Ignoring duplicate click on Download button")
          return
        }
        downloadButton.dataset.clicked = "true"

        const originalHref = downloadButton.href
        console.log("🎯 Download button clicked - Activity Logged! Redirecting to:", originalHref)

        // Log activity and then redirect
        logActivityButtonClick("download", originalHref)
      })

      console.log("✅ Download button tracking setup complete")
    }

    // Find Watch button (next sibling or similar button)
    const watchButtons = document.querySelectorAll('a[href*="/aiovg_videos/"]')
    watchButtons.forEach((watchButton, index) => {
      if (!watchButton.dataset.activityHandlerAdded) {
        watchButton.dataset.activityHandlerAdded = "true"

        watchButton.addEventListener("click", (event) => {
          event.preventDefault()
          event.stopPropagation()

          // Prevent double-clicks
          if (watchButton.dataset.clicked === "true") {
            console.log("🔘 Ignoring duplicate click on Watch button")
            return
          }
          watchButton.dataset.clicked = "true"

          const originalHref = watchButton.href
          console.log("🎯 Watch button clicked - Activity Logged! Redirecting to:", originalHref)

          // Log activity and then redirect
          logActivityButtonClick("watch", originalHref)
        })

        console.log(`✅ Watch button ${index + 1} tracking setup complete`)
      }
    })

    // Setup observer for dynamically added activity buttons
    setupDynamicActivityButtonTracking()
  } catch (error) {
    console.error("❌ Error setting up activity button tracking:", error)
  }
}

// Setup sidebar links activity streak tracking
function setupSidebarLinksTracking() {
  try {
    const sidebarLinks = document.querySelectorAll(".sidebar__links")
    if (!sidebarLinks.length) return

    console.log(`🔘 Setting up sidebar links tracking for ${sidebarLinks.length} element(s)...`)

    sidebarLinks.forEach((link) => {
      if (link.dataset.sidebarHandlerAdded) return
      link.dataset.sidebarHandlerAdded = "true"

      link.addEventListener("click", () => {
        if (!window.phpData?.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
          console.warn("⚠️ No owner ID available, cannot track sidebar link click")
          return
        }

        console.log("🎯 Sidebar link clicked - logging activity streak...")
        logSidebarLinkActivity()
      })
    })

    console.log("✅ Sidebar links tracking setup complete")
  } catch (error) {
    console.error("❌ Error setting up sidebar links tracking:", error)
  }
}

async function logSidebarLinkActivity() {
  try {
    const formData = new FormData()
    formData.append("action", "log_sidebar_activity")
    formData.append("ownerId", window.phpData.ownerIdFromPHP)

    const response = await fetch(window.phpData.ajaxUrl, {
      method: "POST",
      body: formData,
    })

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    const result = await response.json()
    console.log("📥 Sidebar link activity log response:", result)

    if (result.success) {
      console.log("✅ Sidebar link activity streak logged successfully!")

      if (window.phpData) {
        window.phpData.activityCount = result.newActivityCount
      }

      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "✅ Activity Streak!",
          text: `Total activities: ${result.newActivityCount}`,
          icon: "success",
          toast: true,
          position: "bottom-start",
          timer: 5000,
          timerProgressBar: true,
          showConfirmButton: false,
          showCloseButton: true,
        })
      }

      const activityLoggedEvent = new CustomEvent("activityVisitLogged", {
        detail: {
          newActivityCount: result.newActivityCount,
          message: result.message,
        },
      })
      document.dispatchEvent(activityLoggedEvent)

      // Check for milestone unlock
      const unlockedMilestone = MILESTONES.activities.find((m) => m.count === result.newActivityCount)
      if (unlockedMilestone) {
        const nextMilestone = MILESTONES.activities.find((m) => m.count > result.newActivityCount)
        const remaining = nextMilestone ? nextMilestone.count - result.newActivityCount : 0
        const config = STREAK_CONFIGS.activities
        const milestonePosition = getMilestonePosition(MILESTONES.activities, result.newActivityCount)
        const ordinalPosition = getWrittenOrdinal(milestonePosition)

        const htmlContent = `
          <div style="text-align: center; padding: 10px;">
            <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
            <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
              <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
              <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
            </div>
            <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
            <p style="margin-top:15px; font-size: 16px; color:#444;">
              ${config.actionText} ${remaining > 0 ? `${remaining} more ${config.itemType} to earn your next book.` : "You have reached the final tier!"}
            </p>
          </div>
        `

        setTimeout(() => {
          Swal.fire({
            html: htmlContent,
            showConfirmButton: true,
            confirmButtonText: "Keep Going! 🦉",
            confirmButtonColor: config.color,
            width: "400px",
          })
        }, 1500)
      }
    } else {
      console.log("ℹ️ Sidebar link activity:", result.message)
    }
  } catch (error) {
    console.error("❌ Error logging sidebar link activity:", error)
  }
}

// Setup dynamic activity button tracking
let _activityObserverCreated = false
function setupDynamicActivityButtonTracking() {
  if (_activityObserverCreated) return
  _activityObserverCreated = true
  try {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.addedNodes.length) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              // Check for download button
              if (node.id === "download-activity" && !node.dataset.activityHandlerAdded) {
                setupSingleActivityButton(node, "download")
              }

              // Check for watch buttons
              if (node.href && node.href.includes("/aiovg_videos/") && !node.dataset.activityHandlerAdded) {
                setupSingleActivityButton(node, "watch")
              }

              // Check children
              const newDownloadButtons = node.querySelectorAll?.("#download-activity") || []
              newDownloadButtons.forEach((btn) => {
                if (!btn.dataset.activityHandlerAdded) {
                  setupSingleActivityButton(btn, "download")
                }
              })

              const newWatchButtons = node.querySelectorAll?.('a[href*="/aiovg_videos/"]') || []
              newWatchButtons.forEach((btn) => {
                if (!btn.dataset.activityHandlerAdded) {
                  setupSingleActivityButton(btn, "watch")
                }
              })
            }
          })
        }
      })
    })

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    })

    console.log("✅ Dynamic activity button tracking initialized")
  } catch (error) {
    console.error("❌ Error setting up dynamic activity button tracking:", error)
  }
}

// Helper function to setup single activity button
function setupSingleActivityButton(button, type) {
  button.dataset.activityHandlerAdded = "true"

  button.addEventListener("click", (event) => {
    event.preventDefault()
    event.stopPropagation()

    if (button.dataset.clicked === "true") {
      console.log(`🔘 Ignoring duplicate click on ${type} button`)
      return
    }
    button.dataset.clicked = "true"

    const originalHref = button.href
    console.log(`🎯 ${type} button clicked - Activity Logged! Redirecting to:`, originalHref)

    logActivityButtonClick(type, originalHref)
  })

  console.log(`✅ ${type} button tracking setup complete`)
}

// Function to log activity button click
async function logActivityButtonClick(type, redirectUrl) {
  try {
    console.log(`🔘 Logging ${type} activity...`)

    // Check if we have owner ID
    if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
      console.warn("⚠️ No owner ID available, redirecting without logging...")
      window.location.href = redirectUrl
      return
    }

    const formData = new FormData()
    formData.append("action", "log_activity_visit")
    formData.append("ownerId", window.phpData.ownerIdFromPHP)
    formData.append("currentUrl", window.location.href)
    formData.append("activityType", type)

    const response = await fetch(window.phpData.ajaxUrl, {
      method: "POST",
      body: formData,
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    console.log(`📥 ${type} activity log response:`, result)

    if (result.success) {
      console.log(`✅ ${type} activity logged successfully!`)

      if (window.phpData) {
        window.phpData.activityCount = result.newActivityCount
      }

      // Check for milestone unlock
      const unlockedMilestone = MILESTONES.activities.find((m) => m.count === result.newActivityCount)
      if (unlockedMilestone) {
        const nextMilestone = MILESTONES.activities.find((m) => m.count > result.newActivityCount)
        const remaining = nextMilestone ? nextMilestone.count - result.newActivityCount : 0
        const config = STREAK_CONFIGS.activities
        const milestonePosition = getMilestonePosition(MILESTONES.activities, result.newActivityCount)
        const ordinalPosition = getWrittenOrdinal(milestonePosition)

        const htmlContent = `
          <div style="text-align: center; padding: 10px;">
            <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
            <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
              <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
              <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
            </div>
            <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
            <p style="margin-top:15px; font-size: 16px; color:#444;">
              ${config.actionText} ${remaining > 0 ? `${remaining} more ${config.itemType} to earn your next book.` : "You have reached the final tier!"}
            </p>
          </div>
        `

        const resultSwal = await Swal.fire({
          html: `
            <style>
              .swal2-cancel.swal-cancel-btn {
                color: #0d6b58 !important;
                box-shadow: none !important;
                margin-top: -30px !important;
                padding: 10px 25px !important;
              }
              .swal2-confirm.swal-confirm-btn {
                color: #0d6b58 !important;
                box-shadow: none !important;
                margin-top: -30px !important;
                padding: 10px 25px !important;
              }
            </style>
            ${htmlContent}
          `,
          width: 450,
          background: "rgba(255, 255, 255, 0.8)",
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: "View My Shelf",
          cancelButtonText: "Okay",
          confirmButtonColor: "transparent",
          cancelButtonColor: "transparent",
          allowOutsideClick: true,
          didDismiss: (dismiss) => {
            console.log("Modal dismissed:", dismiss)
          },
          customClass: {
            popup: "book-unlock-modal",
            confirmButton: "swal-confirm-btn",
            cancelButton: "swal-cancel-btn",
          },
        })

        if (resultSwal.isConfirmed) {
          window.location.href = "/bookshelf/"
        } else {
          // Redirect after 2 seconds if they didn't click "View My Shelf"
          setTimeout(() => {
            window.location.href = redirectUrl
          }, 2000)
        }
      } else {
        // No milestone, just redirect
        window.location.href = redirectUrl
      }
    } else {
      console.warn(`⚠️ ${type} activity logging failed:`, result.message)
      window.location.href = redirectUrl
    }
  } catch (error) {
    console.error(`❌ Error logging ${type} activity:`, error)
    window.location.href = redirectUrl
  }
}

// Function to setup language button tracking
function setupLanguageButtonTracking() {
  try {
    console.log("🔘 Setting up language button tracking...")

    // Get all buttons on the page
    const buttons = document.querySelectorAll("button, a")

    buttons.forEach((button) => {
      if (!button.dataset.languageHandlerAdded) {
        button.dataset.languageHandlerAdded = "true"

        button.addEventListener("click", (event) => {
          const currentUrl = window.location.href
          const pathname = window.location.pathname

          if (checkIfLanguagePage(pathname) && !button.dataset.clicked) {
            button.dataset.clicked = "true"
            console.log("📍 Language button clicked on language page")

            logLanguageVisit()

            // Reset the flag after 500ms to allow re-clicking
            setTimeout(() => {
              button.dataset.clicked = "false"
            }, 500)
          }
        })
      }
    })

    console.log("✅ Language button tracking setup complete")

    // Also setup observer for dynamically added buttons
    setupDynamicLanguageButtonTracking()
  } catch (error) {
    console.error("❌ Error setting up language button tracking:", error)
  }
}

// Setup dynamic language button tracking
let _languageObserverCreated = false
function setupDynamicLanguageButtonTracking() {
  if (_languageObserverCreated) return
  _languageObserverCreated = true
  try {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.addedNodes.length) {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
              // Check if it's a button or link
              if (node.tagName === "BUTTON" || node.tagName === "A") {
                if (!node.dataset.languageHandlerAdded) {
                  setupSingleLanguageButton(node)
                }
              }

              // Check children
              const newButtons = node.querySelectorAll?.("button, a") || []
              newButtons.forEach((btn) => {
                if (!btn.dataset.languageHandlerAdded) {
                  setupSingleLanguageButton(btn)
                }
              })
            }
          })
        }
      })
    })

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    })

    console.log("✅ Dynamic language button tracking initialized")
  } catch (error) {
    console.error("❌ Error setting up dynamic language button tracking:", error)
  }
}

// Helper function to setup single language button
function setupSingleLanguageButton(button) {
  button.dataset.languageHandlerAdded = "true"

  button.addEventListener("click", (event) => {
    const pathname = window.location.pathname

    if (checkIfLanguagePage(pathname) && !button.dataset.clicked) {
      button.dataset.clicked = "true"
      console.log("📍 Language button clicked on language page")

      logLanguageVisit()

      // Reset the flag after 500ms to allow re-clicking
      setTimeout(() => {
        button.dataset.clicked = "false"
      }, 500)
    }
  })

  console.log("✅ Language button tracking setup complete for single button")
}

// Function to log language visit
async function logLanguageVisit(redirectUrl = null) {
  try {
    // Check if we have owner ID
    if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
      console.warn("⚠️ No owner ID available, cannot log language visit")
      if (redirectUrl) window.location.href = redirectUrl
      return
    }

    const currentLanguagePath = extractLanguagePath(window.location.pathname)

    if (!currentLanguagePath) {
      console.warn("⚠️ Not on a valid language page, skipping language logging...")
      return
    }

    const sessionKey = `languageVisited_${currentLanguagePath.replace(/\//g, "_")}`
    const alreadyLogged = sessionStorage.getItem(sessionKey)

    let languageCount = window.phpData?.languageCount || 0
    let isNew = false

    if (alreadyLogged === "true") {
      console.log("🔄 Language already logged in this session, skipping logging...")
    } else {
      const formData = new FormData()
      formData.append("action", "log_language_visit")
      formData.append("ownerId", window.phpData.ownerIdFromPHP)
      formData.append("currentUrl", window.location.href)

      const response = await fetch(window.phpData.ajaxUrl, {
        method: "POST",
        body: formData,
      })

      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
      const result = await response.json()
      console.log("📥 Language visit log response:", result)

      if (result.success || result.alreadyVisited) {
        languageCount = result.newLanguageCount ?? languageCount
        isNew = result.success && !result.alreadyVisited

        if (result.success) {
          if (window.phpData) {
            window.phpData.languageCount = languageCount
          }
          sessionStorage.setItem(sessionKey, "true")

          if (typeof Swal !== "undefined") {
            Swal.fire({
              title: "🌐 New Language Explored!",
              text: `Total languages: ${languageCount}`,
              icon: "success",
              toast: true,
              position: "bottom-start",
              timer: 5000,
              timerProgressBar: true,
              showConfirmButton: false,
              showCloseButton: true,
            })
          }

          const languageLoggedEvent = new CustomEvent("languageVisitLogged", {
            detail: {
              newLanguageCount: languageCount,
              languagePath: result.languagePath,
              message: result.message,
            },
          })
          document.dispatchEvent(languageLoggedEvent)

          // Notify bookshelf to refresh language count display
          document.dispatchEvent(new CustomEvent("languageCountUpdated", {
            detail: { languageCount: languageCount },
          }))
        }
      } else {
        console.warn("⚠️ Language visit logging failed:", result.message)
      }
    }

    // 🎉 Check for milestone unlock 
    const unlockedMilestone = isNew ? MILESTONES.languages.find((m) => m.count === languageCount) : null
    if (unlockedMilestone) {
      const nextMilestone = MILESTONES.languages.find((m) => m.count > languageCount)
      const remaining = nextMilestone ? nextMilestone.count - languageCount : 0
      const config = STREAK_CONFIGS.languages
      const milestonePosition = getMilestonePosition(MILESTONES.languages, languageCount)
      const ordinalPosition = getWrittenOrdinal(milestonePosition)

      const htmlContent = `
        <div style="text-align: center; padding: 10px;">
          <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
          <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
            <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
            <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
          </div>
          <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
          <p style="margin-top:15px; font-size: 16px; color:#444;">
            ${config.actionText} ${remaining > 0 ? `${remaining} more ${config.itemType} to earn your next book.` : "You have reached the final tier!"}
          </p>
        </div>
      `

      const resultSwal = await Swal.fire({
        html: `
        <style>
          .swal2-cancel.swal-cancel-btn {
            color: #0d6b58 !important;
            box-shadow: none !important;
            margin-top: -30px !important;
            padding: 10px 25px !important;
          }
          .swal2-confirm.swal-confirm-btn {
            color: #0d6b58 !important;
            box-shadow: none !important;
            margin-top: -30px !important;
            padding: 10px 25px !important;
          }
        </style>
        ${htmlContent}
      `,
        width: 450,
        background: "rgba(255, 255, 255, 0.8)",
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: "View My Shelf",
        cancelButtonText: "Okay",
        confirmButtonColor: "transparent",
        cancelButtonColor: "transparent",
        allowOutsideClick: true,
        didDismiss: (dismiss) => {
          console.log("Modal dismissed:", dismiss)
        },
        customClass: {
          popup: "book-unlock-modal",
          confirmButton: "swal-confirm-btn",
          cancelButton: "swal-cancel-btn",
        },
      })

      if (resultSwal.isConfirmed) {
        window.location.href = "/bookshelf/"
      }
    }

  } catch (error) {
    console.error("❌ Error logging language visit:", error)
    if (redirectUrl) window.location.href = redirectUrl
  }
}

// Function to log activity visit
async function logActivityVisit() {
  try {
    // Check if we have owner ID
    if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
      console.warn("⚠️ No owner ID available, cannot track activity")
      return
    }

    const formData = new FormData()
    formData.append("action", "log_activity_visit")
    formData.append("ownerId", window.phpData.ownerIdFromPHP)
    formData.append("currentUrl", window.location.href)

    const response = await fetch(window.phpData.ajaxUrl, {
      method: "POST",
      body: formData,
    })

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    const result = await response.json()
    console.log("📥 Activity visit log response:", result)

    if (result.success) {
      console.log("✅ Activity logged successfully!")

      if (window.phpData) {
        window.phpData.activityCount = result.newActivityCount
        if (result.newPoints !== undefined) window.phpData.points = result.newPoints
      }

      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "✅ Activity Logged!",
          text: result.newPoints !== undefined
            ? `+1 feather! Total: ${result.newPoints} 🪶`
            : `Total activities: ${result.newActivityCount}`,
          icon: "success",
          toast: true,
          position: "bottom-start",
          timer: 5000,
          timerProgressBar: true,
          showConfirmButton: false,
          showCloseButton: true,
        })
      }

      // Dispatch custom event
      const activityLoggedEvent = new CustomEvent("activityVisitLogged", {
        detail: {
          newActivityCount: result.newActivityCount,
          newPoints: result.newPoints,
          message: result.message,
        },
      })
      document.dispatchEvent(activityLoggedEvent)

      // Check for milestone unlock
      const unlockedMilestone = MILESTONES.activities.find((m) => m.count === result.newActivityCount)
      if (unlockedMilestone) {
        const nextMilestone = MILESTONES.activities.find((m) => m.count > result.newActivityCount)
        const remaining = nextMilestone ? nextMilestone.count - result.newActivityCount : 0
        const config = STREAK_CONFIGS.activities
        const milestonePosition = getMilestonePosition(MILESTONES.activities, result.newActivityCount)
        const ordinalPosition = getWrittenOrdinal(milestonePosition)

        const htmlContent = `
          <div style="text-align: center; padding: 10px;">
            <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
            <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
              <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
              <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
            </div>
            <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
            <p style="margin-top:15px; font-size: 16px; color:#444;">
              ${config.actionText} ${remaining > 0 ? `${remaining} more ${config.itemType} to earn your next book.` : "You have reached the final tier!"}
            </p>
          </div>
        `

        const resultSwal = await Swal.fire({
          html: `
            <style>
              .swal2-cancel.swal-cancel-btn {
                color: #0d6b58 !important;
                box-shadow: none !important;
                margin-top: -30px !important;
                padding: 10px 25px !important;
              }
              .swal2-confirm.swal-confirm-btn {
                color: #0d6b58 !important;
                box-shadow: none !important;
                margin-top: -30px !important;
                padding: 10px 25px !important;
              }
            </style>
            ${htmlContent}
          `,
          width: 450,
          background: "rgba(255, 255, 255, 0.8)",
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: "View My Shelf",
          cancelButtonText: "Okay",
          confirmButtonColor: "transparent",
          cancelButtonColor: "transparent",
          allowOutsideClick: true,
          didDismiss: (dismiss) => {
            console.log("Modal dismissed:", dismiss)
          },
          customClass: {
            popup: "book-unlock-modal",
            confirmButton: "swal-confirm-btn",
            cancelButton: "swal-cancel-btn",
          },
        })

        if (resultSwal.isConfirmed) {
          window.location.href = "/bookshelf/"
        }
      }
    } else {
      console.warn("⚠️ Activity visit logging failed:", result.message)
    }
  } catch (error) {
    console.error("❌ Error logging activity visit:", error)
  }
}

// Function to log book visit
async function logBookVisit() {
  try {
    // Check if we have owner ID
    if (!window.phpData.ownerIdFromPHP || window.phpData.ownerIdFromPHP === "null") {
      console.warn("⚠️ No owner ID available, cannot track book")
      return
    }

    const formData = new FormData()
    formData.append("action", "log_book_visit")
    formData.append("ownerId", window.phpData.ownerIdFromPHP)
    formData.append("currentUrl", window.location.href)

    const response = await fetch(window.phpData.ajaxUrl, {
      method: "POST",
      body: formData,
    })

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    const result = await response.json()
    console.log("📥 Book visit log response:", result)

    if (result.success) {
      console.log("✅ Book logged successfully!")

      if (window.phpData) {
        window.phpData.bookCount = result.newBookCount
        if (result.newPoints !== undefined) window.phpData.points = result.newPoints
      }

      if (result.newPoints !== undefined && typeof Swal !== "undefined") {
        Swal.fire({
          title: "📚 Book Logged!",
          text: `+1 feather! Total: ${result.newPoints} 🪶`,
          icon: "success",
          toast: true,
          position: "bottom-start",
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          showCloseButton: true,
        })
      }

      // Dispatch custom event
      const bookLoggedEvent = new CustomEvent("bookVisitLogged", {
        detail: {
          newBookCount: result.newBookCount,
          newPoints: result.newPoints,
          message: result.message,
        },
      })
      document.dispatchEvent(bookLoggedEvent)

      // Check for milestone unlock
      const unlockedMilestone = MILESTONES.books.find((m) => m.count === result.newBookCount)
      if (unlockedMilestone) {
        const nextMilestone = MILESTONES.books.find((m) => m.count > result.newBookCount)
        const remaining = nextMilestone ? nextMilestone.count - result.newBookCount : 0
        const config = STREAK_CONFIGS.books
        const milestonePosition = getMilestonePosition(MILESTONES.books, result.newBookCount)
        const ordinalPosition = getWrittenOrdinal(milestonePosition)

        const htmlContent = `
          <div style="text-align: center; padding: 10px;">
            <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
            <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
              <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
              <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
            </div>
            <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
            <p style="margin-top:15px; font-size: 16px; color:#444;">
              ${config.actionText} ${remaining > 0 ? `${remaining} more ${config.itemType} to earn your next book.` : "You have reached the final tier!"}
            </p>
          </div>
        `

        const resultSwal = await Swal.fire({
          html: `
            <style>
              .swal2-cancel.swal-cancel-btn {
                color: #0d6b58 !important;
                box-shadow: none !important;
                margin-top: -30px !important;
                padding: 10px 25px !important;
              }
              .swal2-confirm.swal-confirm-btn {
                color: #0d6b58 !important;
                box-shadow: none !important;
                margin-top: -30px !important;
                padding: 10px 25px !important;
              }
            </style>
            ${htmlContent}
          `,
          width: 450,
          background: "rgba(255, 255, 255, 0.8)",
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: "View My Shelf",
          cancelButtonText: "Okay",
          confirmButtonColor: "transparent",
          cancelButtonColor: "transparent",
          allowOutsideClick: true,
          didDismiss: (dismiss) => {
            console.log("Modal dismissed:", dismiss)
          },
          customClass: {
            popup: "book-unlock-modal",
            confirmButton: "swal-confirm-btn",
            cancelButton: "swal-cancel-btn",
          },
        })

        if (resultSwal.isConfirmed) {
          window.location.href = "/bookshelf/"
        }
      }
    } else {
      console.warn("⚠️ Book visit logging failed:", result.message)
    }
  } catch (error) {
    console.error("❌ Error logging book visit:", error)
  }
}

// Guards: prevent concurrent calls and duplicate fires per day
let _membersHomeVisitInFlight = false

// Function to log members home visit
async function logMembersHomeVisit() {
  try {
    const today = new Date().toDateString()
    const ownerId = window.phpData.ownerIdFromPHP
    const sessionKey = `lastMembersHomeDate_${ownerId}`

    // Already logged today for this user — skip silently
    if (sessionStorage.getItem(sessionKey) === today) {
      console.log("🔄 Already logged members home visit today, skipping...")
      return
    }

    // Prevent concurrent in-flight requests (race condition guard)
    if (_membersHomeVisitInFlight) {
      console.log("🔄 Members home visit already in-flight, skipping...")
      return
    }

    // Set both guards immediately before the fetch
    _membersHomeVisitInFlight = true
    sessionStorage.setItem(sessionKey, today)

    const formData = new FormData()
    formData.append("action", "log_members_home_visit")
    formData.append("ownerId", window.phpData.ownerIdFromPHP)
    formData.append("currentUrl", window.location.href)

    const response = await fetch(window.phpData.ajaxUrl, {
      method: "POST",
      body: formData,
    })

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    const result = await response.json()
    console.log("📥 Members home visit log response:", result)

    if (result.success) {
      const days = result.newDaysCount
      const milestones = [
        { day: 1, title: "Hatchling", next: 3 },
        { day: 3, title: "Owlet", next: 5 },
        { day: 5, title: "Flapper", next: 10 },
        { day: 10, title: "Soarer", next: 20 },
        { day: 20, title: "Sky Flyer", next: 30 },
        { day: 30, title: "Hunter", next: 50 },
        { day: 50, title: "Hootmaster", next: 75 },
        { day: 75, title: "Sky Hero", next: 100 },
        { day: 100, title: "Wise Owl", next: 150 },
        { day: 150, title: "Starwing", next: null },
      ]
      const unlockedMilestone = result.isNewDay ? milestones.find((m) => m.day === days) : null
      if (unlockedMilestone) {
        const config = STREAK_CONFIGS.app
        const milestonePosition = milestones.findIndex((m) => m.day === days) + 1
        const ordinalPosition = getWrittenOrdinal(milestonePosition)
        const remainingDays = unlockedMilestone.next ? unlockedMilestone.next - days : 0

        const htmlContent = `
          <div style="text-align: center; padding: 10px;">
            <h2 style="color:${config.color}; font-size: 28px; margin-bottom: 10px;">🎉 Congratulations! 🎉</h2>
            <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
              <img src="${config.imageUrl}" alt="${unlockedMilestone.title}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px;" />
              <div style="color: #000; font-size: 20px; font-weight: normal;">${unlockedMilestone.title}</div>
            </div>
            <p style="font-size: 18px; margin: 15px 0;">You've earned your ${ordinalPosition} ${config.categoryName} book!</p>
            <p style="margin-top:15px; font-size: 16px; color:#444;">
              ${config.actionText} ${remainingDays > 0 ? `${remainingDays} more day${remainingDays > 1 ? "s" : ""} to earn your next book.` : "You have reached the final tier!"}
            </p>
          </div>
        `

        // Check if barcode is a trial before showing streak popup
        let shouldShowPopup = true
        if (
          typeof l4k_vars !== "undefined" &&
          l4k_vars.library_barcode &&
          !window.location.search.includes("comments-only")
        ) {
          try {
            const formData = new FormData()
            formData.append("action", "l4k_checkIfBarcodeIsTrial")
            formData.append("page_id", l4k_vars.page_id)
            formData.append("barcode", l4k_vars.library_barcode)

            const trialResponse = await fetch(window.phpData.ajaxUrl, {
              method: "POST",
              body: formData,
            })
            const trialResult = await trialResponse.json()
            console.log(trialResult)
            // status === 1 means trial — skip popup to avoid double stacking
            shouldShowPopup = trialResult.status !== 1
          } catch (error) {
            console.error("❌ Error checking trial barcode:", error)
            shouldShowPopup = true // show popup if the check fails
          }
        }

        if (shouldShowPopup) {
	        const resultSwal = await Swal.fire({
	          html: `
	        <style>
	          .swal2-cancel.swal-cancel-btn {
	            color: #0d6b58 !important;
	            box-shadow: none !important;
	            margin-top: -30px !important;
	            padding: 10px 25px !important;
	          }
	          .swal2-confirm.swal-confirm-btn {
	            color: #0d6b58 !important;
	            box-shadow: none !important;
	            margin-top: -30px !important;
	            padding: 10px 25px !important;
	          }
	        </style>
	        ${htmlContent}
	      `,
	          width: 450,
	          background: "rgba(255, 255, 255, 0.8)",
	          showConfirmButton: true,
	          showCancelButton: true,
	          confirmButtonText: "View My Shelf",
	          cancelButtonText: "Okay",
	          confirmButtonColor: "transparent",
	          cancelButtonColor: "transparent",
	          allowOutsideClick: true,
	          didDismiss: (dismiss) => {
	            console.log("Modal dismissed:", dismiss)
	          },
	          customClass: {
	            popup: "book-unlock-modal",
	            confirmButton: "swal-confirm-btn",
	            cancelButton: "swal-cancel-btn",
	          },
	        })
	    }

        if (resultSwal.isConfirmed) {
          window.location.href = "/bookshelf/"
        }
      }

if (result.isNewDay && typeof Swal !== "undefined") {
  Swal.fire({
    title: "✅ Daily Visit Logged!",
    text: `Streak: ${days} day${days > 1 ? "s" : ""}`,
    icon: "success",
    toast: true,
    position: "bottom-start",
    timer: 5000,
    timerProgressBar: true,
    showConfirmButton: false,
    showCloseButton: true,
  });
}


      // Dispatch custom event
      const membersHomeLoggedEvent = new CustomEvent("membersHomeVisitLogged", {
        detail: {
          newDaysCount: days,
          message: result.message,
        },
      })
      document.dispatchEvent(membersHomeLoggedEvent)
    }
  } catch (error) {
    console.error("❌ Error logging members home visit:", error)
    // Clear date guard on error so a retry is possible on next visit
    sessionStorage.removeItem(`lastMembersHomeDate_${window.phpData.ownerIdFromPHP}`)
  } finally {
    _membersHomeVisitInFlight = false
  }
}

// MutationObserver for dynamic page changes — only sets up button handlers,
// never triggers tracking calls (those fire from initializeTracking only).
new MutationObserver(() => {
  setupActivityButtonTracking()
}).observe(document.body, { childList: true, subtree: true })

// Also re-initialize tracking if navigation changes
if (window.history && window.history.pushState) {
  const originalPushState = window.history.pushState
  window.history.pushState = function () {
    const result = originalPushState.apply(this, arguments)
    setTimeout(initializeTracking, 100)
    return result
  }
}

if (window.history && window.history.replaceState) {
  const originalReplaceState = window.history.replaceState
  window.history.replaceState = function () {
    const result = originalReplaceState.apply(this, arguments)
    setTimeout(initializeTracking, 100)
    return result
  }
}

// Setup popstate listener for back/forward navigation
window.addEventListener("popstate", () => {
  setTimeout(initializeTracking, 100) // Small delay to ensure DOM updates
})

console.log("🎯 Activities & Books tracking script initialized successfully!")

// Event listeners for debugging and monitoring
document.addEventListener("languageVisitLogged", (event) => {
  console.log("🌐 Language Visit Event:", event.detail)
})

document.addEventListener("membersHomeVisitLogged", (event) => {
  console.log("🎊 Members Home Visit Event:", event.detail)
})

document.addEventListener("activityVisitLogged", (event) => {
  console.log("🎯 Activity Visit Event:", event.detail)
})

document.addEventListener("bookVisitLogged", (event) => {
  console.log("📚 Book Visit Event:", event.detail)
})

// added script
 function getActivityCount() {
            const activityCountElement = document.getElementById('activityCount');
            return activityCountElement ? parseInt(activityCountElement.textContent) || 0 : 0;
        }
        function getBookCount() {
            const bookCountElement = document.getElementById('bookCount');
            return bookCountElement ? parseInt(bookCountElement.textContent) || 0 : 0;
        }
        function getDaysCount() {
            const daysCountElement = document.getElementById('daysCount');
            return daysCountElement ? parseInt(daysCountElement.textContent) || 0 : 0;
        }
        function getLanguageCount() {
            const languageCountElement = document.getElementById('languageCount');
            return languageCountElement ? parseInt(languageCountElement.textContent) || 0 : 0;
        }
        let ACTIVITY_COUNT = getActivityCount();
        let BOOK_COUNT = getBookCount();
        let DAYS_COUNT = getDaysCount();
        let LANGUAGE_COUNT = (window.phpData && window.phpData.languageCount != null)
            ? (parseInt(window.phpData.languageCount) || 0)
            : getLanguageCount();
        let currentBookData = {};
        let bookOpened = false;

        const appUsageData = [
            { name: 'Hatchling', required: 1, description: 'Just hatched and beginning your streak journey.' },
            { name: 'Owlet', required: 3, description: 'A little owl learning new things every day.' },
            { name: 'Flapper', required: 5, description: 'Starting to flap your wings and explore your growing streak.' },
            { name: 'Soarer', required: 7, description: 'Flying higher and getting stronger with each new streak.' },
            { name: 'Sky Flyer', required: 14, description: 'Gliding towards learning and streak mastery.' },
            { name: 'Hunter', required: 21, description: 'Growing sharper and braver — focused like an owl on the hunt.' },
            { name: 'Hootmaster', required: 30, description: 'A clever and confident streak leader.' },
            { name: 'Sky Hero', required: 45, description: 'Flying fast and far — a true hero of the streak.' },
            { name: 'Wise Owl', required: 60, description: 'An experienced learner.' },
            { name: 'Starwing', required: 100, description: 'A legendary streak owl whose wings reach the stars.' }
        ];

        const activitiesData = [
            { name: 'Hatchling', required: 1, description: 'Just hatched and beginning your streak journey.' },
            { name: 'Owlet', required: 3, description: 'A little owl learning new things every day.' },
            { name: 'Flapper', required: 5, description: 'Starting to flap your wings and explore your growing streak.' },
            { name: 'Soarer', required: 10, description: 'Flying higher and getting stronger with each new streak.' },
            { name: 'Sky Flyer', required: 15, description: 'Gliding towards learning and streak mastery.' },
            { name: 'Hunter', required: 20, description: 'Growing sharper and braver — focused like an owl on the hunt.' },
            { name: 'Hootmaster', required: 30, description: 'A clever and confident streak leader.' },
            { name: 'Sky Hero', required: 40, description: 'Flying fast and far — a true hero of the streak.' },
            { name: 'Wise Owl', required: 50, description: 'An experienced learner.' },
            { name: 'Starwing', required: 75, description: 'A legendary streak owl whose wings reach the stars.' }
        ];

        const booksData = [
            { name: 'Hatchling', required: 1, description: 'Just hatched and beginning your streak journey.' },
            { name: 'Owlet', required: 3, description: 'A little owl learning new things every day.' },
            { name: 'Flapper', required: 5, description: 'Starting to flap your wings and explore your growing streak.' },
            { name: 'Soarer', required: 10, description: 'Flying higher and getting stronger with each new streak.' },
            { name: 'Sky Flyer', required: 20, description: 'Gliding towards learning and streak mastery.' },
            { name: 'Hunter', required: 30, description: 'Growing sharper and braver — focused like an owl on the hunt.' },
            { name: 'Hootmaster', required: 50, description: 'A clever and confident streak leader.' },
            { name: 'Sky Hero', required: 75, description: 'Flying fast and far — a true hero of the streak.' },
            { name: 'Wise Owl', required: 100, description: 'An experienced learner.' },
            { name: 'Starwing', required: 150, description: 'A legendary streak owl whose wings reach the stars.' }
        ];

        const languagesData = [
            { name: 'Hatchling', required: 1, description: 'Just hatched and beginning your language journey.' },
            { name: 'Owlet', required: 3, description: 'A little owl learning new languages every day.' },
            { name: 'Flapper', required: 5, description: 'Starting to flap your wings and explore your growing language skills.' },
            { name: 'Soarer', required: 7, description: 'Flying higher and getting stronger with each new language.' },
            { name: 'Sky Flyer', required: 10, description: 'Gliding towards language mastery.' },
            { name: 'Hunter', required: 12, description: 'Growing sharper and braver — focused like an owl on the hunt for languages.' },
            { name: 'Hootmaster', required: 15, description: 'A clever and confident language leader.' },
            { name: 'Sky Hero', required: 18, description: 'Flying fast and far — a true hero of languages.' },
            { name: 'Wise Owl', required: 20, description: 'An experienced polyglot learner.' },
            { name: 'Starwing', required: 25, description: 'A legendary language owl whose wings reach the stars.' }
        ];

        function updateActivityCount() {
            const newActivityCount = getActivityCount();
            if (newActivityCount !== ACTIVITY_COUNT) {
                ACTIVITY_COUNT = newActivityCount;
                initializeActivitiesStreak();
            }
        }
        function updateBookCount() {
            const newBookCount = getBookCount();
            if (newBookCount !== BOOK_COUNT) {
                BOOK_COUNT = newBookCount;
                initializeBooksStreak();
            }
        }
        function updateDaysCount() {
            const newDaysCount = getDaysCount();
            if (newDaysCount !== DAYS_COUNT) {
                DAYS_COUNT = newDaysCount;
                initializeAppUsageStreak();
            }
        }
        function updateLanguageCount() {
            const newLanguageCount = getLanguageCount();
            if (newLanguageCount !== LANGUAGE_COUNT) {
                LANGUAGE_COUNT = newLanguageCount;
                initializeLanguagesStreak();
            }
        }

        function initializeAppUsageStreak() {
            const appUsageBooks = document.querySelectorAll('[data-category="App Usage Streak"]');
            appUsageBooks.forEach((book, index) => {
                if (index < appUsageData.length) {
                    const appUsage = appUsageData[index];
                    book.setAttribute('data-required', appUsage.required);
                    book.setAttribute('data-description', appUsage.description);
                    if (DAYS_COUNT >= appUsage.required) {
                        book.classList.remove('disabled');
                    } else {
                        book.classList.add('disabled');
                    }
                }
            });
        }
        function initializeActivitiesStreak() {
            const activitiesBooks = document.querySelectorAll('[data-category="Activities Streak"]');
            activitiesBooks.forEach((book, index) => {
                if (index < activitiesData.length) {
                    const activity = activitiesData[index];
                    book.setAttribute('data-required', activity.required);
                    book.setAttribute('data-description', activity.description);
                    if (ACTIVITY_COUNT >= activity.required) {
                        book.classList.remove('disabled');
                    } else {
                        book.classList.add('disabled');
                    }
                }
            });
        }
        function initializeBooksStreak() {
            const booksBooks = document.querySelectorAll('[data-category="Books Streak"]');
            booksBooks.forEach((book, index) => {
                if (index < booksData.length) {
                    const bookData = booksData[index];
                    book.setAttribute('data-required', bookData.required);
                    book.setAttribute('data-description', bookData.description);
                    if (BOOK_COUNT >= bookData.required) {
                        book.classList.remove('disabled');
                    } else {
                        book.classList.add('disabled');
                    }
                }
            });
        }
        function initializeLanguagesStreak() {
            const languagesBooks = document.querySelectorAll('[data-category="Languages Explored"]');
            languagesBooks.forEach((book, index) => {
                if (index < languagesData.length) {
                    const language = languagesData[index];
                    book.setAttribute('data-required', language.required);
                    book.setAttribute('data-description', language.description);
                    if (LANGUAGE_COUNT >= language.required) {
                        book.classList.remove('disabled');
                    } else {
                        book.classList.add('disabled');
                    }
                }
            });
        }

        function openModal(bookTitle, category, color, required, description, isDisabled) {
            const modal = document.getElementById('bookModal');
            const modalHeader = document.getElementById('modalHeader');
            const modalTitle = document.getElementById('modalTitle');
            const modalBook = document.getElementById('modalBook');
            const modalBookTitle = document.getElementById('modalBookTitle');
            const modalDescription = document.getElementById('modalDescription');
            const bookOpeningOverlay = document.getElementById('bookOpeningOverlay');
            const book3DContainer = document.getElementById('book3DContainer');
            const tapToOpen = document.getElementById('tapToOpen');
            const closeBtn = document.getElementById('closeBtn');
            const closeBookBtn = document.getElementById('closeBookBtn');

            currentBookData = { bookTitle, category, color, required, description, isDisabled };
            bookOpened = false;

            bookOpeningOverlay.classList.remove('show');
            book3DContainer.classList.remove('opening');
            modalBook.style.display = 'flex';
            tapToOpen.style.display = 'inline-block';
            closeBtn.style.display = 'block';
            closeBookBtn.style.display = 'none';

            modalTitle.textContent = category;
            modalBookTitle.textContent = bookTitle;
            modalDescription.textContent = description || 'No description available.';

             let headerColor, bookColor;
            switch(color) {
                case 'blue':
                    headerColor = '#4994EC';
                    bookColor = '#4994EC';
                    break;
                case 'green':
                    headerColor = '#67AD5B';
                    bookColor = '#67AD5B';
                    break;
                case 'purple':
                    headerColor = '#8F31AA';
                    bookColor = '#8F31AA';
                    break;
                case 'gold':
                    headerColor = '#EE9B37';
                    bookColor = '#EE9B37';
                    break;
            }


            modalHeader.style.background = headerColor;
            modalDescription.style.background = headerColor;
            closeBtn.style.background = '#f90';
            closeBtn.style.color = '#1E1E1E';
            closeBookBtn.style.background = '#f90';
            closeBookBtn.style.color = '#1E1E1E';
            modalBook.style.background = bookColor;

            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        function openBook() {
            if (bookOpened) return;
            
            const bookOpeningOverlay = document.getElementById('bookOpeningOverlay');
            const book3DContainer = document.getElementById('book3DContainer');
            const modalBook = document.getElementById('modalBook');
            const closeBtn = document.getElementById('closeBtn');
            const closeBookBtn = document.getElementById('closeBookBtn');
            const bookPageLeft = document.getElementById('bookPageLeft');
            const bookPageRight = document.getElementById('bookPageRight');

            let textColor;
            switch(currentBookData.color) {
                case 'blue': textColor = '#4994EC'; break;
                case 'green': textColor = '#67AD5B'; break;
                case 'purple': textColor = '#8F31AA'; break;
                case 'gold': textColor = '#EE9B37'; break;
                default: textColor = '#333';
            }

            if (currentBookData.isDisabled) {
                let remaining, currentCount, activityText;
                if (currentBookData.category === 'Books Streak') {
                    remaining = currentBookData.required - BOOK_COUNT;
                    currentCount = BOOK_COUNT;
                    activityText = remaining === 1 ? 'book' : 'books';
                } else if (currentBookData.category === 'App Usage Streak') {
                    remaining = currentBookData.required - DAYS_COUNT;
                    currentCount = DAYS_COUNT;
                    activityText = remaining === 1 ? 'day' : 'days';
                } else if (currentBookData.category === 'Languages Explored') {
                    remaining = currentBookData.required - LANGUAGE_COUNT;
                    currentCount = LANGUAGE_COUNT;
                    activityText = remaining === 1 ? 'language' : 'languages';
                } else {
                    remaining = currentBookData.required - ACTIVITY_COUNT;
                    currentCount = ACTIVITY_COUNT;
                    activityText = remaining === 1 ? 'activity' : 'activities';
                }
                bookPageLeft.innerHTML = `
                    <div class="disabled-book-content">To earn this book you must complete ${remaining} more ${activityText} (non-consecutive).</div>
                `;
                bookPageRight.innerHTML = `
                 <div class="earn-it-section" style="position: relative; z-index: 9999 !important;">
    <a href="/member-home" class="arrow-link">➜</a>
    <a href="/member-home" class="earn-it-text">Go now! Earn it!</a>
</div>

                `;
            } else {
                let activityText, categoryText;
                if (currentBookData.category === 'Books Streak') {
                    activityText = currentBookData.required === 1 ? 'book' : 'books';
                    categoryText = 'reading';
                } else if (currentBookData.category === 'App Usage Streak') {
                    activityText = currentBookData.required === 1 ? 'day' : 'days';
                    categoryText = 'using the app for';
                } else if (currentBookData.category === 'Languages Explored') {
                    activityText = currentBookData.required === 1 ? 'language' : 'languages';
                    categoryText = 'exploring';
                } else {
                    activityText = currentBookData.required === 1 ? 'activity' : 'activities';
                    categoryText = 'completing';
                }
                bookPageLeft.innerHTML = `
                    <div class="achievement-text">You have earned this streak by ${categoryText} ${currentBookData.required} ${activityText}!</div>
                `;
                let emoji = `<img src="https://staging32.lote4kids.com/wp-content/uploads/2025/11/activities.png" alt="Activities" style="width: 24px; height: 24px; vertical-align: middle;" />`;
                if (currentBookData.category === 'Books Streak') {
                    emoji = `<img src="https://staging32.lote4kids.com/wp-content/uploads/2025/11/book-streak.png" alt="Book Streak" style="width: 24px; height: 24px; vertical-align: middle;" />`;
                } else if (currentBookData.category === 'App Usage Streak') {
                    emoji = `<img src="https://staging32.lote4kids.com/wp-content/uploads/2025/11/app-streak.png" alt="App Streak" style="width: 24px; height: 24px; vertical-align: middle;" />`;
                } else if (currentBookData.category === 'Languages Explored') {
                    emoji = `<img src="https://staging32.lote4kids.com/wp-content/uploads/2025/11/languages-explored.png" alt="Languages Explored" style="width: 24px; height: 24px; vertical-align: middle;" />`;
                }
                bookPageRight.innerHTML = `
                    <div class="congratulations">${emoji}<br>Great job!</div>
                `;
            }

            modalBook.style.display = 'none';
            closeBtn.style.display = 'none';
            closeBookBtn.style.display = 'block';
            bookOpeningOverlay.classList.add('show');
            
            setTimeout(() => {
                book3DContainer.classList.add('opening');
                bookOpened = true;

                const updatedArrowLink = bookPageRight.querySelector('.arrow-link');
                const updatedEarnItText = bookPageRight.querySelector('.earn-it-text');
                if (updatedArrowLink) updatedArrowLink.style.color = textColor;
                if (updatedEarnItText) updatedEarnItText.style.color = textColor;
                const congrats = bookPageRight.querySelector('.congratulations, .disabled-book-content');
                if (congrats) congrats.style.color = textColor;
            }, 200);
        }

        function closeBook() {
            const bookOpeningOverlay = document.getElementById('bookOpeningOverlay');
            const book3DContainer = document.getElementById('book3DContainer');
            const modalBook = document.getElementById('modalBook');
            const tapToOpen = document.getElementById('tapToOpen');
            const closeBtn = document.getElementById('closeBtn');
            const closeBookBtn = document.getElementById('closeBookBtn');

            bookOpeningOverlay.classList.remove('show');
            book3DContainer.classList.remove('opening');
            bookOpened = false;

            modalBook.style.display = 'flex';
            tapToOpen.style.display = 'inline-block';
            closeBtn.style.display = 'block';
            closeBookBtn.style.display = 'none';
        }

        function closeModal() {
            const modal = document.getElementById('bookModal');
            const bookOpeningOverlay = document.getElementById('bookOpeningOverlay');
            const book3DContainer = document.getElementById('book3DContainer');
            const modalBook = document.getElementById('modalBook');
            const tapToOpen = document.getElementById('tapToOpen');
            const closeBtn = document.getElementById('closeBtn');
            const closeBookBtn = document.getElementById('closeBookBtn');

            bookOpeningOverlay.classList.remove('show');
            book3DContainer.classList.remove('opening');
            bookOpened = false;

            modalBook.style.display = 'flex';
            tapToOpen.style.display = 'inline-block';
            closeBtn.style.display = 'block';
            closeBookBtn.style.display = 'none';

            modal.style.display = 'none';
            modal.classList.remove('show');
        }

        function initializeStreaksBookshelf() {
            initializeAppUsageStreak();
            initializeActivitiesStreak();
            initializeBooksStreak();
            initializeLanguagesStreak();

            document.querySelectorAll('.shelf:not(.locked) .book').forEach(book => {
                book.addEventListener('click', function() {
                    const bookTitle = this.dataset.book;
                    const category = this.dataset.category;
                    const color = this.dataset.color;
                    const required = parseInt(this.dataset.required);
                    const description = this.dataset.description;
                    const isDisabled = this.classList.contains('disabled');
                    if (bookTitle) openModal(bookTitle, category, color, required, description, isDisabled);
                });
            });

            const bookModal = document.getElementById('bookModal');
            if (bookModal) {
                bookModal.addEventListener('click', function(e) {
                    if (e.target === this) closeModal();
                });
            }
            const bookOpeningOverlay = document.getElementById('bookOpeningOverlay');
            if (bookOpeningOverlay) {
                bookOpeningOverlay.addEventListener('click', function(e) {
                    if (e.target === this) closeBook();
                });
            }

            const activityCountElement = document.getElementById('activityCount');
            const bookCountElement = document.getElementById('bookCount');
            const daysCountElement = document.getElementById('daysCount');
            const languageCountElement = document.getElementById('languageCount');

            if (activityCountElement && window.phpData && typeof window.phpData.activityCount !== 'undefined') {
                activityCountElement.textContent = window.phpData.activityCount;
                updateActivityCount();
            }
            if (bookCountElement && window.phpData && typeof window.phpData.bookCount !== 'undefined') {
                bookCountElement.textContent = window.phpData.bookCount;
                updateBookCount();
            }
            if (daysCountElement && window.phpData && typeof window.phpData.daysCount !== 'undefined') {
                daysCountElement.textContent = window.phpData.daysCount;
                updateDaysCount();
            }
            if (languageCountElement && window.phpData && typeof window.phpData.languageCount !== 'undefined') {
                languageCountElement.textContent = window.phpData.languageCount;
                updateLanguageCount();
            }

            document.addEventListener('activityCountUpdated', e => {
                if (e.detail?.activityCount !== undefined) {
                    activityCountElement.textContent = e.detail.activityCount;
                    updateActivityCount();
                }
            });
            document.addEventListener('bookCountUpdated', e => {
                if (e.detail?.bookCount !== undefined) {
                    bookCountElement.textContent = e.detail.bookCount;
                    updateBookCount();
                }
            });
            document.addEventListener('daysCountUpdated', e => {
                if (e.detail?.daysCount !== undefined) {
                    daysCountElement.textContent = e.detail.daysCount;
                    updateDaysCount();
                }
            });
            document.addEventListener('languageCountUpdated', e => {
                if (e.detail?.languageCount !== undefined) {
                    languageCountElement.textContent = e.detail.languageCount;
                    updateLanguageCount();
                }
            });

            const observer = new MutationObserver(mutations => {
                mutations.forEach(m => {
                    const id = m.target.id;
                    if (['activityCount', 'bookCount', 'daysCount', 'languageCount'].includes(id)) {
                        switch(id) {
                            case 'activityCount': updateActivityCount(); break;
                            case 'bookCount': updateBookCount(); break;
                            case 'daysCount': updateDaysCount(); break;
                            case 'languageCount': updateLanguageCount(); break;
                        }
                    }
                });
            });

            [activityCountElement, bookCountElement, daysCountElement, languageCountElement].forEach(el => {
                if (el) observer.observe(el, { childList: true, characterData: true, subtree: true });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeStreaksBookshelf);
        } else {
            initializeStreaksBookshelf();
        }

        function goBackOrHome() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "/member-home/";
            }
        }
 
        document.querySelectorAll('.book').forEach(book => {
            const title = book.querySelector('.book-title');
            if (title && title.textContent.trim() === "") {
                book.style.cursor = "default";
            }
        });