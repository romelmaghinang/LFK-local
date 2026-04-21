import { initializeApp } from "https://www.gstatic.com/firebasejs/11.9.0/firebase-app.js"
import { getStorage, ref, listAll, getDownloadURL } from "https://www.gstatic.com/firebasejs/11.9.0/firebase-storage.js"
import { getFirestore, collection, getDocs } from "https://www.gstatic.com/firebasejs/11.9.0/firebase-firestore.js"

// Debug: check cache-busting version
if (typeof window.lektsiConfig !== "undefined") {
  console.log("Avatar Creator loaded, build version:", window.lektsiConfig.buildVersion);
}

;(() => {
  const originalLog = console.log
  console.log = (...args) => {
    try {
      if (args[0] === "Character Data:" && typeof args[1] === "object") {
        const data = args[1]
        applyCharacterLayers(data)
        window.characterData = data
      }
    } catch (err) {
      originalLog("❌ Error processing Character Data:", err)
    }
    originalLog.apply(console, args)
  }

  function applyCharacterLayers(data) {
    const baseImg = document.querySelector('img[src*="default_body.png"]')
    if (!baseImg) return

    const container = baseImg.parentElement
    container.style.position = "relative"
    container.style.display = "flex"
    container.style.justifyContent = "center"
    container.style.alignItems = "center"

    const existingLayers = container.querySelectorAll('img[data-character-layer]')
    existingLayers.forEach(el => el.remove())

    const baseWidth = baseImg.width || 200
    const baseHeight = baseImg.height || 300

    const layers = [
      { key: "selectedBottomUrl", z: 2 },
      { key: "selectedShoesUrl", z: 3, adjustY: -5 },
      { key: "selectedTopUrl", z: 4 },
      { key: "selectedHeadUrl", z: 5, adjustY: -5 },
      { key: "selectedAccessoryUrl", z: 6 },
    ]

    let hasLayer = false

    layers.forEach((layer) => {
      if (data[layer.key]) {
        hasLayer = true
        const img = document.createElement("img")
        img.src = data[layer.key]
        img.style.position = "absolute"
        img.style.width = baseWidth + "px"
        img.style.height = baseHeight + "px"
        img.style.zIndex = layer.z
        img.style.transform = `translateY(${-20 + (layer.adjustY || 0)}px) translateX(${layer.adjustX || 0}px)`
        img.setAttribute('data-character-layer', layer.key)
        container.appendChild(img)
      }
    })

    if (!hasLayer) {
      baseImg.src = "https://firebasestorage.googleapis.com/v0/b/lote4kids-gamification.firebasestorage.app/o/default_body.png?alt=media&token=57faff74-435c-43ba-8871-2ae533debcc8"
    }
  }

  setTimeout(() => {
    if (window.characterData) {
      applyCharacterLayers(window.characterData)
    }
  }, 50)
})()

// Cache configuration
const CACHE_CONFIG = {
  PREFIX: "firebase_images_cache_",
  EXPIRY_HOURS: 99999, // Cache expires
  VERSION: "1.0", // Increment this to invalidate all caches
}

// Cache utility functions
const CacheManager = {
  // Generate cache key for a folder
  getCacheKey(folderPath) {
    return `${CACHE_CONFIG.PREFIX}${folderPath.replace(/[^a-zA-Z0-9]/g, "_")}`
  },

  // Get cache metadata key
  getMetaKey(folderPath) {
    return `${this.getCacheKey(folderPath)}_meta`
  },

  // Check if cache exists and is valid
  isCacheValid(folderPath) {
    try {
      const metaKey = this.getMetaKey(folderPath)
      const meta = localStorage.getItem(metaKey)

      if (!meta) {
        console.log(`📦 No cache found for ${folderPath}`)
        return false
      }

      const { timestamp, version } = JSON.parse(meta)
      const now = Date.now()
      const expiryTime = timestamp + CACHE_CONFIG.EXPIRY_HOURS * 60 * 60 * 1000

      // Check version compatibility
      if (version !== CACHE_CONFIG.VERSION) {
        console.log(`🔄 Cache version mismatch for ${folderPath}, invalidating`)
        this.clearCache(folderPath)
        return false
      }

      // Check expiry
      if (now > expiryTime) {
        console.log(`⏰ Cache expired for ${folderPath}`)
        this.clearCache(folderPath)
        return false
      }

      console.log(`✅ Valid cache found for ${folderPath}`)
      return true
    } catch (error) {
      console.error(`❌ Error checking cache validity for ${folderPath}:`, error)
      this.clearCache(folderPath)
      return false
    }
  },

  // Get cached images for a folder
  getCachedImages(folderPath) {
    try {
      if (!this.isCacheValid(folderPath)) {
        return null
      }

      const cacheKey = this.getCacheKey(folderPath)
      const cachedData = localStorage.getItem(cacheKey)

      if (!cachedData) {
        return null
      }

      const images = JSON.parse(cachedData)
      console.log(`📥 Loaded ${images.length} images from cache for ${folderPath}`)
      return images
    } catch (error) {
      console.error(`❌ Error loading cache for ${folderPath}:`, error)
      this.clearCache(folderPath)
      return null
    }
  },

  // Save images to cache
  setCachedImages(folderPath, images) {
    try {
      const cacheKey = this.getCacheKey(folderPath)
      const metaKey = this.getMetaKey(folderPath)

      // Save images data
      localStorage.setItem(cacheKey, JSON.stringify(images))

      // Save metadata
      const meta = {
        timestamp: Date.now(),
        version: CACHE_CONFIG.VERSION,
        count: images.length,
      }
      localStorage.setItem(metaKey, JSON.stringify(meta))

      console.log(`💾 Cached ${images.length} images for ${folderPath}`)
    } catch (error) {
      console.error(`❌ Error saving cache for ${folderPath}:`, error)
      // If localStorage is full, try to clear some old caches
      this.cleanupOldCaches()
    }
  },

  // Clear cache for specific folder
  clearCache(folderPath) {
    try {
      const cacheKey = this.getCacheKey(folderPath)
      const metaKey = this.getMetaKey(folderPath)

      localStorage.removeItem(cacheKey)
      localStorage.removeItem(metaKey)

      console.log(`🗑️ Cleared cache for ${folderPath}`)
    } catch (error) {
      console.error(`❌ Error clearing cache for ${folderPath}:`, error)
    }
  },

  // Clear all Firebase image caches
  clearAllCaches() {
    try {
      const keysToRemove = []

      for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i)
        if (key && key.startsWith(CACHE_CONFIG.PREFIX)) {
          keysToRemove.push(key)
        }
      }

      keysToRemove.forEach((key) => localStorage.removeItem(key))
      console.log(`🗑️ Cleared ${keysToRemove.length} cache entries`)
    } catch (error) {
      console.error("❌ Error clearing all caches:", error)
    }
  },

  // Clean up old or invalid caches
  cleanupOldCaches() {
    try {
      const now = Date.now()
      const keysToRemove = []

      for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i)
        if (key && key.startsWith(CACHE_CONFIG.PREFIX) && key.endsWith("_meta")) {
          try {
            const meta = JSON.parse(localStorage.getItem(key))
            const expiryTime = meta.timestamp + CACHE_CONFIG.EXPIRY_HOURS * 60 * 60 * 1000

            if (now > expiryTime || meta.version !== CACHE_CONFIG.VERSION) {
              // Remove both meta and data keys
              const dataKey = key.replace("_meta", "")
              keysToRemove.push(key, dataKey)
            }
          } catch (error) {
            // Invalid meta, remove it
            keysToRemove.push(key)
          }
        }
      }

      keysToRemove.forEach((key) => localStorage.removeItem(key))
      if (keysToRemove.length > 0) {
        console.log(`🧹 Cleaned up ${keysToRemove.length} old cache entries`)
      }
    } catch (error) {
      console.error("❌ Error during cache cleanup:", error)
    }
  },

  // Get cache statistics
  getCacheStats() {
    const stats = {
      totalEntries: 0,
      totalSize: 0,
      folders: [],
    }

    try {
      for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i)
        if (key && key.startsWith(CACHE_CONFIG.PREFIX)) {
          const value = localStorage.getItem(key)
          stats.totalEntries++
          stats.totalSize += value.length

          if (key.endsWith("_meta")) {
            try {
              const meta = JSON.parse(value)
              const folderName = key.replace(CACHE_CONFIG.PREFIX, "").replace("_meta", "")
              stats.folders.push({
                folder: folderName,
                count: meta.count,
                timestamp: new Date(meta.timestamp).toLocaleString(),
                version: meta.version,
              })
            } catch (error) {
              // Invalid meta entry
            }
          }
        }
      }
    } catch (error) {
      console.error("❌ Error getting cache stats:", error)
    }

    return stats
  },
}

// SweetAlert2 will be loaded via script tag or use window.Swal directly
function getAjaxUrl() {
  if (window.phpData && window.phpData.ajaxUrl) {
    return window.phpData.ajaxUrl
  }
  if (typeof window.ajaxurl !== "undefined") {
    return window.ajaxurl
  }
  if (window.wp && window.wp.ajax && window.wp.ajax.settings && window.wp.ajax.settings.url) {
    return window.wp.ajax.settings.url
  }
  const currentUrl = window.location.origin
  return currentUrl + "/wp-admin/admin-ajax.php"
}

const AJAX_URL = getAjaxUrl()
const phpData = window.phpData || {}

console.log("PHP Data:", phpData)
console.log("Owner ID for JS (from PHP):", phpData.ownerIdFromPHP || "undefined")
console.log("Character Data for JS (from PHP):", phpData.characterDataFromPHP || "undefined")
console.log("WordPress AJAX URL:", AJAX_URL)

const isAvatarCreationPage = !!(
  document.getElementById("messageContainer") &&
  document.getElementById("mainContainer") &&
  document.getElementById("avatarCharacter")
)

const isVideoPage = window.location.pathname.includes("/aiovg_videos/")
const isActivityPage = window.location.pathname.includes("/activities/")

console.log("🔍 Page Detection:")
console.log("Is Avatar Creation Page:", isAvatarCreationPage)
console.log("Is Video Page:", isVideoPage)
console.log("Is Activity Page:", isActivityPage)
console.log("Current URL:", window.location.href)

// Firebase app and services will be initialized after getting config from PHP
let app = null
let storage = null
let db = null
let currentCategory = "headwear"
const allImages = {}
let selectedItems = {}
let boughtItems = []
let boughtItemsWithCategories = {}
let currentPoints = 0
let currentOwnerId = null
let characterData = null
let selectedLanguage = "en"
let pageStartTime = null
let isLoading = false
let isAssetsLoaded = false

// Add immediate tab setup
document.addEventListener('DOMContentLoaded', () => {
  // Set default tab as active
  setTimeout(() => {
    const defaultTab = document.querySelector(`.category-tab[data-category="${currentCategory}"]`)
    if (defaultTab) {
      defaultTab.classList.add("active")
      console.log("🎯 Set default tab active on DOM ready:", currentCategory)
    }
  }, 100)
  
  // Set loading state
  isLoading = true
})

// Enhanced cache initialization
const initializeCache = () => {
  try {
    // Check if localStorage is available
    if (typeof(Storage) !== "undefined") {
      console.log("✅ Browser cache is available")
      
      // Log current cache status
      const cacheStats = CacheManager.getCacheStats()
      if (cacheStats.totalFolders > 0) {
        console.log(`📦 Found existing cache with ${cacheStats.totalImages} images across ${cacheStats.totalFolders} folders`)
      }
      
      return true
    } else {
      console.warn("⚠️ Browser cache not available - will download assets every time")
      return false
    }
  } catch (error) {
    console.error("❌ Error initializing cache:", error)
    return false
  }
}

const categories = {
  headwear: ["hats", "head"],
  tops: ["top", "tops", "ties"],
  shoes: ["shoes"],
  facewear: ["glasses", "accessories"],
  bottoms: ["bottom", "bottoms"],
  unlocked: [],
}

const categoryPrices = {
  headwear: 1,
  facewear: 2,
  tops: 5,
  bottoms: 5,
  shoes: 3,
  unlocked: 0,
}

const messageContainer = document.getElementById("messageContainer")
const messageTitle = document.getElementById("messageTitle")
const messageText = document.getElementById("messageText")
const mainContainer = document.getElementById("mainContainer")
const avatarCharacterEl = document.getElementById("avatarCharacter")
const itemsContainer = document.getElementById("items-container")
const categoryTitleEl = document.getElementById("categoryTitle")
const unlockedItemsGrid = document.getElementById("unlockedItemsGrid")
const userCreditsEl = document.getElementById("userCredits")
const saveAvatarBtn = document.getElementById("saveAvatarBtn")
const unlockedCountEl = document.getElementById("unlockedCount")
const languageModalOverlay = document.getElementById("languageModalOverlay")
const languageModalContent = document.getElementById("languageModalContent")
const closeLanguageModalBtn = document.getElementById("closeLanguageModalBtn")
const languageOptionButtons = document.querySelectorAll(".language-option-button")

console.log("🎯 DOM Elements Found:")
console.log("messageContainer:", !!messageContainer)
console.log("mainContainer:", !!mainContainer)
console.log("avatarCharacterEl:", !!avatarCharacterEl)
console.log("itemsContainer:", !!itemsContainer)

// IMMEDIATE DISPLAY: Show main container right away to prevent blank screen
if (mainContainer) {
  mainContainer.classList.add("show")
  console.log("✅ Main container shown immediately on page load")
}

// Show initial loading state in items container
if (itemsContainer) {
  itemsContainer.innerHTML = '<div style="text-align: center; padding: 40px; color: #666; font-size: 16px;">Loading avatar creator...</div>'
}

// Initialize cache cleanup on page load
CacheManager.cleanupOldCaches()

// Log cache statistics
const cacheStats = CacheManager.getCacheStats()
console.log("📊 Cache Statistics:", cacheStats)

// MODIFIED: Function to get Firebase config from PHP with better error handling
async function getFirebaseConfig() {
  try {
    console.log("🔥 Fetching Firebase config from PHP...")
    const formData = new FormData()
    formData.append("action", "get_firebase_config")
    formData.append("ownerId", currentOwnerId || 0)

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    if (!response.ok) {
      console.error("❌ HTTP Error:", response.status, response.statusText)
      return null
    }

    const responseText = await response.text()
    let result
    try {
      result = JSON.parse(responseText)
    } catch (parseError) {
      console.error("❌ JSON Parse Error:", parseError)
      console.error("❌ Response was not valid JSON. First 500 chars:", responseText.substring(0, 500))
      return null
    }

    if (result.success && result.config) {
      console.log("✅ Firebase config received from PHP")
      return result.config
    } else {
      console.error("❌ Failed to get Firebase config:", result.message)
      return null
    }
  } catch (error) {
    console.error("❌ Error fetching Firebase config:", error)
    return null
  }
}

// Function to initialize Firebase
async function initializeFirebase() {
  try {
    const firebaseConfig = await getFirebaseConfig()
    if (!firebaseConfig) {
      console.error("❌ No Firebase config available")
      return false
    }

    console.log("🔥 Initializing Firebase with config...")
    app = initializeApp(firebaseConfig)
    storage = getStorage(app)
    db = getFirestore(app)

    console.log("✅ Firebase initialized successfully!")
    console.log("✅ Firebase Storage connected!")
    console.log("✅ Firebase Firestore connected!")
    return true
  } catch (error) {
    console.error("❌ Error initializing Firebase:", error)
    return false
  }
}

// Function to fetch assets data from Firestore gamification database
async function fetchAssetsFromFirestore() {
  if (!db) {
    console.error("❌ Firestore not initialized")
    return
  }

  try {
    console.log("🔍 Fetching assets data from Firestore...")
    console.log("📍 Accessing path: gamification/assets")

    const assetsCollection = collection(db, "gamification", "assets")
    const assetsSnapshot = await getDocs(assetsCollection)

    console.log("📊 Assets data from Firestore gamification database:")
    if (assetsSnapshot.empty) {
      console.log("📭 No assets found in gamification/assets collection")
      return
    }

    const assetsData = []
    const pathsData = []
    const languagesData = []

    console.log(`📦 Found ${assetsSnapshot.size} documents in assets collection`)

    assetsSnapshot.forEach((doc) => {
      const data = { id: doc.id, ...doc.data() }
      assetsData.push(data)

      if (data.path) {
        pathsData.push({
          id: doc.id,
          path: data.path,
          type: data.type || "unknown",
          location: data.location || "unknown",
          name: data.name || "unknown",
        })
        console.log(`🛤️ Path found: ${data.path} (Type: ${data.type}, Location: ${data.location})`)
      }

      if (data.language) {
        languagesData.push({
          id: doc.id,
          language: data.language,
          name: data.name || "unknown",
          path: data.path || "unknown",
        })
        console.log(`🌐 Language found: ${data.language} (Name: ${data.name})`)
      }

      console.log("📄 Complete asset document:", data)
    })

    console.log(`✅ Successfully fetched ${assetsData.length} assets from Firestore gamification database`)

    const pathsByType = {}
    pathsData.forEach((item) => {
      if (!pathsByType[item.type]) {
        pathsByType[item.type] = []
      }
      pathsByType[item.type].push(item)
    })

    const languageGroups = {}
    languagesData.forEach((item) => {
      if (!languageGroups[item.language]) {
        languageGroups[item.language] = []
      }
      languageGroups[item.language].push(item)
    })

    console.log("🗂️ Paths grouped by type:", pathsByType)
    console.log("🌍 Assets grouped by language:", languageGroups)

    return {
      assets: assetsData,
      paths: pathsData,
      languages: languagesData,
      pathsByType: pathsByType,
      languageGroups: languageGroups,
    }
  } catch (error) {
  }
}

function createNotificationContainer() {
  if (document.getElementById("custom-notifications")) return

  const container = document.createElement("div")
  container.id = "custom-notifications"
  container.style.cssText = `
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 10000;
    pointer-events: none;
  `
  document.body.appendChild(container)
}


function showNotification(message, type = "success") {
  createNotificationContainer()

  const notification = document.createElement("div")
  notification.style.cssText = `
    background: ${type === "success" ? "#4CAF50" : type === "error" ? "#f44336" : "#2196F3"};
    color: white;
    padding: 12px 20px;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
    font-weight: 500;
    max-width: 300px;
    word-wrap: break-word;
    pointer-events: auto;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    opacity: 0;
    cursor: pointer;
    z-index: 999999999;
  `

  const icon = type === "success" ? "✓" : type === "error" ? "✕" : "ℹ"
  notification.innerHTML = `<span style="margin-right: 8px;">${icon}</span>${message}`

  const container = document.getElementById("custom-notifications")
  container.appendChild(notification)

  setTimeout(() => {
    notification.style.transform = "translateX(0)"
    notification.style.opacity = "1"
  }, 10)

  notification.addEventListener("click", () => {
    removeNotification(notification)
  })

  setTimeout(() => {
    removeNotification(notification)
  }, 4000)
}

function removeNotification(notification) {
  notification.style.transform = "translateX(100%)"
  notification.style.opacity = "0"
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification)
    }
  }, 300)
}

function showInsufficientFundsModal(category) {
  const itemPrice = categoryPrices[category] || 5
  const storiesNeeded = itemPrice - currentPoints

  if (!document.getElementById("swal2-custom-style")) {
    const style = document.createElement("style")
    style.id = "swal2-custom-style"
    style.innerHTML = `
      .swal2-custom-title {
        font-weight: normal;
        font-size: 16px;
      }
    `
    document.head.appendChild(style)
  }

  // Check if SweetAlert2 is available globally
  if (typeof window !== "undefined" && window.Swal) {
    window.Swal.fire({
      title: `You don't have enough feathers to unlock this item.<br>
      Read ${storiesNeeded} more ${storiesNeeded === 1 ? "story" : "stories"} to unlock this item!`,
      icon: "warning",
      confirmButtonText: "OK",
      confirmButtonColor: "#f90",
      customClass: {
        title: "swal2-custom-title",
        confirmButton: "no-border-btn",
      },
    })
  } else {
    // Fallback to native alert if SweetAlert2 is not available
    alert(
      `You don't have enough feathers to unlock this item. Read ${storiesNeeded} more ${storiesNeeded === 1 ? "story" : "stories"} to unlock this item!`,
    )
  }
}

function organizeBoughtItemsByCategory() {
  boughtItemsWithCategories = {}

  for (const [category, folders] of Object.entries(categories)) {
    if (category === "unlocked") continue
    boughtItemsWithCategories[category] = []

    folders.forEach((folder) => {
      if (allImages[folder]) {
        allImages[folder].forEach((imageUrl) => {
          if (boughtItems.includes(imageUrl)) {
            boughtItemsWithCategories[category].push(imageUrl)
          }
        })
      }
    })
  }

  console.log("🗂️ Organized bought items by category:", boughtItemsWithCategories)
}

async function processPurchase(imageUrl, category) {
  const itemPrice = categoryPrices[category] || 5
  if (currentPoints < itemPrice) {
    showInsufficientFundsModal(category)
    return { success: false, message: "Insufficient funds" }
  }

  if (!currentOwnerId) {
    showNotification("No owner ID set", "error")
    return { success: false, message: "No owner ID" }
  }

  try {
    const formData = new FormData()
    formData.append("action", "purchase_item")
    formData.append("ownerId", currentOwnerId)
    formData.append("itemUrl", imageUrl)
    formData.append("category", category)

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()

    if (result.success) {
      currentPoints = result.newPoints

      if (!boughtItems.includes(imageUrl)) {
        boughtItems.push(imageUrl)
      }

      organizeBoughtItemsByCategory()
      updateCreditsDisplay()
      showNotification("Item unlocked successfully!", "success")
      
      // FIXED: Refresh the current category display to show Equip button instead of lock
      displayCategory(currentCategory)
      
      return { success: true, newPoints: result.newPoints, message: result.message }
    } else {
      showNotification(result.message || "Purchase failed", "error")
      return { success: false, message: result.message || "Purchase failed" }
    }
  } catch (error) {
    console.error("Purchase error:", error)
    showNotification("Purchase failed", "error")
    return { success: false, message: "Purchase failed due to network error" }
  }
}

function showCongratulationsModal(imageUrl, category) {
  const existingStyle = document.getElementById("glow-animation-style")
  if (existingStyle) {
    existingStyle.remove()
  }

  const modalOverlay = document.createElement("div")
  modalOverlay.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.3s ease;
  `

  const modalContent = document.createElement("div")
  modalContent.style.cssText = `
    background: white;
    border-radius: 15px;
    padding: 30px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    transform: scale(0.7);
    transition: transform 0.3s ease;
    position: relative;
  `

  const closeBtn = document.createElement("button")
  closeBtn.innerHTML = "×"
  closeBtn.style.cssText = `
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
  `

  closeBtn.addEventListener("click", () => {
    modalOverlay.style.opacity = "0"
    modalContent.style.transform = "scale(0.7)"
    setTimeout(() => {
      document.body.removeChild(modalOverlay)
      const style = document.getElementById("glow-animation-style")
      if (style) style.remove()
    }, 300)
  })

  modalContent.appendChild(closeBtn)

  const title = document.createElement("h3")
  title.textContent = "Congratulations!"
  title.style.cssText = `
    margin: 0 0 15px 0;
    color: #333;
    font-size: 28px;
    font-weight: 500;
  `
  modalContent.appendChild(title)

  const description = document.createElement("p")
  description.innerHTML = `You've unlocked a new item! What would you like to do with it?`
  description.style.cssText = `
    margin: 0 0 20px 0;
    color: #666;
    font-size: 16px;
    line-height: 1.5;
  `
  modalContent.appendChild(description)

  const previewContainer = document.createElement("div")
  previewContainer.style.cssText = `
    width: 200px;
    height: 200px;
    margin: 0 auto 20px;
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    background: #f0f0f0;
  `

  const baseCharacter = document.createElement("div")
  baseCharacter.style.cssText = `
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('https://firebasestorage.googleapis.com/v0/b/lote4kids-gamification.firebasestorage.app/o/default_body.png?alt=media&token=57faff74-435c-43ba-8871-2ae533debcc8');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    z-index: 1;
  `
  previewContainer.appendChild(baseCharacter)

  const zIndexMap = {
    bottoms: 2,
    tops: 3,
    shoes: 4,
    headwear: 5,
    facewear: 6,
  }

  for (const cat in selectedItems) {
    if (selectedItems[cat] && cat !== category) {
      const itemLayer = document.createElement("div")
      itemLayer.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('${selectedItems[cat]}');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        z-index: ${zIndexMap[cat] || 0};
      `
      previewContainer.appendChild(itemLayer)
    }
  }

  const newItemLayer = document.createElement("div")
  newItemLayer.style.cssText = `
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('${imageUrl}');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    z-index: ${zIndexMap[category] || 0};
    animation: glow 2s ease-in-out infinite alternate;
  `
  previewContainer.appendChild(newItemLayer)

  const style = document.createElement("style")
  style.id = "glow-animation-style"
  style.textContent = `
    @keyframes glow {
      from { filter: drop-shadow(0 0 5px #ff6b35); }
      to { filter: drop-shadow(0 0 15px #ff6b35); }
    }
  `
  document.head.appendChild(style)

  modalContent.appendChild(previewContainer)

  const buttonsContainer = document.createElement("div")
  buttonsContainer.style.cssText = `
    display: flex;
    gap: 15px;
    justify-content: center;
  `

  const useItBtn = document.createElement("button")
  useItBtn.textContent = "Use it"
  useItBtn.style.cssText = `
    background: #f90;
    color: #1E1E1E;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
  `

  useItBtn.onmouseover = () => (useItBtn.style.background = "#e88a00")
  useItBtn.onmouseout = () => (useItBtn.style.background = "#f90")

  useItBtn.addEventListener("click", async () => {
    await handleItemSelection(imageUrl, category, true)
    modalOverlay.style.opacity = "0"
    modalContent.style.transform = "scale(0.7)"
    setTimeout(() => {
      document.body.removeChild(modalOverlay)
      const style = document.getElementById("glow-animation-style")
      if (style) style.remove()
    }, 300)
  })

  buttonsContainer.appendChild(useItBtn)

  const storeBtn = document.createElement("button")
  storeBtn.textContent = "Store"
  storeBtn.style.cssText = `
    background: #1e1e1e;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
  `

  storeBtn.onmouseover = () => (storeBtn.style.background = "#5a6268")
  storeBtn.onmouseout = () => (storeBtn.style.background = "#1e1e1e")

  storeBtn.addEventListener("click", () => {
    modalOverlay.style.opacity = "0"
    modalContent.style.transform = "scale(0.7)"
    setTimeout(() => {
      document.body.removeChild(modalOverlay)
      const style = document.getElementById("glow-animation-style")
      if (style) style.remove()
    }, 300)
    displayCategory(currentCategory)
  })

  buttonsContainer.appendChild(storeBtn)
  modalContent.appendChild(buttonsContainer)
  modalOverlay.appendChild(modalContent)
  document.body.appendChild(modalOverlay)

  setTimeout(() => {
    modalOverlay.style.opacity = "1"
    modalContent.style.transform = "scale(1)"
  }, 10)
}

function showPurchaseConfirmationModal(imageUrl, category) {
  const itemPrice = categoryPrices[category] || 5

  if (currentPoints < itemPrice) {
    showInsufficientFundsModal(category)
    return
  }

  const categoryNames = {
    headwear: "Headwear",
    tops: "Top",
    shoes: "Shoes",
    facewear: "Facewear",
    bottoms: "Bottom",
  }

  const modalOverlay = document.createElement("div")
  modalOverlay.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.3s ease;
  `

  const modalContent = document.createElement("div")
  modalContent.style.cssText = `
    background: white;
    border-radius: 15px;
    padding: 30px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    transform: scale(0.7);
    transition: transform 0.3s ease;
    position: relative;
  `

  const previewContainer = document.createElement("div")
  previewContainer.style.cssText = `
    width: 200px;
    height: 200px;
    margin: 0 auto 20px;
    position: relative;
    border-radius: 15px;
    overflow: hidden;
  `

  const baseCharacter = document.createElement("div")
  baseCharacter.style.cssText = `
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('https://firebasestorage.googleapis.com/v0/b/lote4kids-gamification.firebasestorage.app/o/default_body.png?alt=media&token=57faff74-435c-43ba-8871-2ae533debcc8');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    z-index: 1;
  `
  previewContainer.appendChild(baseCharacter)

  const zIndexMap = {
    bottoms: 2,
    tops: 3,
    shoes: 4,
    headwear: 5,
    facewear: 6,
  }

  for (const cat in selectedItems) {
    if (selectedItems[cat] && cat !== category) {
      const itemLayer = document.createElement("div")
      itemLayer.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('${selectedItems[cat]}');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        z-index: ${zIndexMap[cat] || 0};
      `
      previewContainer.appendChild(itemLayer)
    }
  }

  const newItemLayer = document.createElement("div")
  newItemLayer.style.cssText = `
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('${imageUrl}');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    z-index: ${zIndexMap[category] || 0};
    animation: glow 2s ease-in-out infinite alternate;
  `
  previewContainer.appendChild(newItemLayer)

  const style = document.createElement("style")
  style.id = "glow-animation-style"
  style.textContent = `
    @keyframes glow {
      from { filter: drop-shadow(0 0 5px #ff6b35); }
      to { filter: drop-shadow(0 0 15px #ff6b35); }
    }
  `
  document.head.appendChild(style)

  const title = document.createElement("h3")
  title.textContent = "Unlock Item"
  title.style.cssText = `
  margin: 0 0 15px 0;
  color: #333;
  font-size: 24px;
  font-family: 'Poppins', sans-serif;
  font-style: normal;
  font-weight: 500;
  `

  const description = document.createElement("p")
  description.innerHTML = `Are you sure you want to unlock this item?</strong><br>`
  description.style.cssText = `
    margin: 0 0 20px 0;
    color: #666;
    font-size: 16px;
    line-height: 1.5;
  `

  const buttonsContainer = document.createElement("div")
  buttonsContainer.style.cssText = `
    display: flex;
    gap: 15px;
    justify-content: center;
  `

  const confirmBtn = document.createElement("button")
  confirmBtn.textContent = "Yes"
  confirmBtn.style.cssText = `
    background: #FF9900;
    color: #1E1E1E;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
  `

  confirmBtn.onmouseover = () => (confirmBtn.style.background = "#e88a00")
  confirmBtn.onmouseout = () => (confirmBtn.style.background = "#f90")

  confirmBtn.addEventListener("click", async () => {
    modalOverlay.style.opacity = "0"
    modalContent.style.transform = "scale(0.7)"
    setTimeout(async () => {
      document.body.removeChild(modalOverlay)
      if (document.head.contains(style)) {
        document.head.removeChild(style)
      }
      const purchaseResult = await processPurchase(imageUrl, category)
      if (purchaseResult.success) {
        showCongratulationsModal(imageUrl, category)
      } else {
        console.error("Purchase failed:", purchaseResult.message)
      }
    }, 300)
  })

  const cancelBtn = document.createElement("button")
  cancelBtn.textContent = "No"
  cancelBtn.style.cssText = `
    background: #1e1e1e;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
  `

  cancelBtn.onmouseover = () => (cancelBtn.style.background = "#5a6268")
  cancelBtn.onmouseout = () => (cancelBtn.style.background = "#1e1e1e")

  cancelBtn.addEventListener("click", () => {
    modalOverlay.style.opacity = "0"
    modalContent.style.transform = "scale(0.7)"
    setTimeout(() => {
      document.body.removeChild(modalOverlay)
      if (document.head.contains(style)) {
        document.head.removeChild(style)
      }
    }, 300)
  })

  modalOverlay.addEventListener("click", (e) => {
    if (e.target === modalOverlay) {
      modalOverlay.style.opacity = "0"
      modalContent.style.transform = "scale(0.7)"
      setTimeout(() => {
        document.body.removeChild(modalOverlay)
        if (document.head.contains(style)) {
          document.head.removeChild(style)
        }
      }, 300)
    }
  })

  buttonsContainer.appendChild(confirmBtn)
  buttonsContainer.appendChild(cancelBtn)
  modalContent.appendChild(title)
  modalContent.appendChild(previewContainer)
  modalContent.appendChild(description)
  modalContent.appendChild(buttonsContainer)
  modalOverlay.appendChild(modalContent)
  document.body.appendChild(modalOverlay)

  setTimeout(() => {
    modalOverlay.style.opacity = "1"
    modalContent.style.transform = "scale(1)"
  }, 10)
}

function isValidVideoPage() {
  const body = document.body
  const html = document.documentElement
  const is404 =
    document.title.toLowerCase().includes("404") ||
    document.title.toLowerCase().includes("not found") ||
    body.classList.contains("error404") ||
    body.classList.contains("page-not-found") ||
    html.classList.contains("error404") ||
    document.querySelector(".error-404") !== null ||
    document.querySelector(".not-found") !== null ||
    document.querySelector("#error-404") !== null

  return !is404
}

// NEW: Function to log 1080 activity when on /avatar-dress-up page
async function log1080Activity() {
  const currentUrl = window.location.href
  const currentPath = window.location.pathname

  console.log("=== 1080 ACTIVITY LOG CHECK ===")
  console.log("Current URL:", currentUrl)
  console.log("Current Path:", currentPath)

  // Check if URL contains /avatar-dress-up
  if (!currentPath.includes("/avatar-dress-up")) {
    console.log("❌ URL does not contain '/avatar-dress-up' - skipping 1080 log")
    return
  }

  console.log("✅ URL contains '/avatar-dress-up' - proceeding with 1080 log")

  if (!currentOwnerId) {
    console.log("❌ Cannot log 1080 activity - No owner ID found")
    return
  }

  console.log("🎯 Attempting to log 1080 activity...")
  console.log("Owner ID:", currentOwnerId)
  console.log("Current URL:", currentUrl)

  try {
    const formData = new FormData()
    formData.append("action", "log_1080_activity")
    formData.append("ownerId", currentOwnerId)
    formData.append("currentUrl", currentUrl)

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()
    console.log("Server response:", result)

    if (result.success) {
      console.log("🎉 SUCCESS! 1080 activity logged!")
      console.log("Web Activity ID:", result.web_activity_id)
      console.log("Barcode:", result.barcode)
      console.log("Message:", result.message)
    } else {
      console.log("❌ Failed to log 1080 activity")
      console.log("Error message:", result.message)
    }
  } catch (error) {
    console.error("❌ Error occurred while logging 1080 activity:", error)
  }

  console.log("=== END 1080 ACTIVITY LOG CHECK ===")
}

/*
async function autoClaimActivityPoints() {
  const currentPagePath = window.location.pathname
  const currentUrl = window.location.href

  console.log("=== ACTIVITY POINTS CHECK ===")
  console.log("Current URL:", currentUrl)
  console.log("Current Path:", currentPagePath)

  if (
    !currentPagePath.includes("/activities/") ||
    currentPagePath === "/activities/color-colouring-pages/" ||
    currentPagePath === "/activities/word-search/" ||
    currentPagePath === "/activities/spot-the-difference/"
  ) {
    console.log("❌ URL is NOT valid for earning points - invalid /activities/ page or excluded path")
    return
  }

  if (!isValidVideoPage()) {
    console.log("❌ URL is NOT valid for earning points - page appears to be 404/not found")
    return
  }

  console.log("✅ URL is VALID for earning points - contains '/activities/' and page exists")

  if (!currentOwnerId) {
    console.log("❌ Cannot claim points - No owner ID found")
    console.log("Owner ID:", currentOwnerId)
    return
  }

  if (!pageStartTime) {
    pageStartTime = Date.now()
    console.log("⏱️ Page timer started")
  }

  const timeOnPage = Date.now() - pageStartTime
  if (timeOnPage < 5000) {
    console.log(`⏳ Need to stay on page for ${Math.ceil((5000 - timeOnPage) / 1000)} more seconds before claiming`)
    setTimeout(
      () => {
        autoClaimActivityPoints()
      },
      5000 - timeOnPage + 100,
    )
    return
  }

  console.log("🎯 Attempting to claim activity points...")
  console.log("Owner ID:", currentOwnerId)
  console.log("Activity URL:", currentUrl)
  console.log(`⏱️ Time on page: ${Math.floor(timeOnPage / 1000)} seconds`)

  try {
    const formData = new FormData()
    formData.append("action", "aiovg_claim_video_points")
    formData.append("ownerId", currentOwnerId)
    formData.append("videoUrl", currentUrl)

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()
    console.log("Server response:", result)

    if (result.success) {
      if (result.newPoints !== undefined) {
        console.log("🎉 SUCCESS! Activity points earned!")
        console.log("Previous points:", currentPoints)
        console.log("New points:", result.newPoints)
        console.log("Points earned:", result.newPoints - currentPoints)
        console.log("Message:", result.message)

        const pointsEarned = result.newPoints - currentPoints
        currentPoints = result.newPoints
      } else {
        console.log("⚠️ No new points earned")
        console.log("Message:", result.message)
      }
    } else {
      console.log("❌ Failed to claim activity points")
      console.log("Error message:", result.message)
    }
  } catch (error) {
    console.error("❌ Error occurred while claiming activity points:", error)
  }

  console.log("=== END ACTIVITY POINTS CHECK ===")
}
*/

/*
async function autoClaimVideoPoints() {
  const currentPagePath = window.location.pathname
  const currentUrl = window.location.href

  console.log("=== VIDEO POINTS CHECK ===")
  console.log("Current URL:", currentUrl)
  console.log("Current Path:", currentPagePath)

  if (!currentPagePath.includes("/aiovg_videos/")) {
    console.log("❌ URL is NOT valid for earning points - does not contain '/aiovg_videos/'")
    console.log("Required pattern: URL must contain '/aiovg_videos/'")
    return
  }

  if (!isValidVideoPage()) {
    console.log("❌ URL is NOT valid for earning points - page appears to be 404/not found")
    return
  }

  console.log("✅ URL is VALID for earning points - contains '/aiovg_videos/' and page exists")

  if (!currentOwnerId) {
    console.log("❌ Cannot claim points - No owner ID found")
    console.log("Owner ID:", currentOwnerId)
    return
  }

  if (!pageStartTime) {
    pageStartTime = Date.now()
    console.log("⏱️ Page timer started")
  }

  const timeOnPage = Date.now() - pageStartTime
  if (timeOnPage < 5000) {
    console.log(`⏳ Need to stay on page for ${Math.ceil((5000 - timeOnPage) / 1000)} more seconds before claiming`)
    setTimeout(
      () => {
        autoClaimVideoPoints()
      },
      5000 - timeOnPage + 100,
    )
    return
  }

  console.log("🎯 Attempting to claim points...")
  console.log("Owner ID:", currentOwnerId)
  console.log("Video URL:", currentUrl)
  console.log(`⏱️ Time on page: ${Math.floor(timeOnPage / 1000)} seconds`)

  try {
    const formData = new FormData()
    formData.append("action", "aiovg_claim_video_points")
    formData.append("ownerId", currentOwnerId)
    formData.append("videoUrl", currentUrl)

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()
    console.log("Server response:", result)

    if (result.success) {
      if (result.newPoints !== undefined) {
        console.log("🎉 SUCCESS! Points earned!")
        console.log("Previous points:", currentPoints)
        console.log("New points:", result.newPoints)
        console.log("Points earned:", result.newPoints - currentPoints)
        console.log("Message:", result.message)

        const pointsEarned = result.newPoints - currentPoints
        currentPoints = result.newPoints
      } else {
        console.log("⚠️ No new points earned")
        console.log("Message:", result.message)
      }
    } else {
      console.log("❌ Failed to claim points")
      console.log("Error message:", result.message)
    }
  } catch (error) {
    console.error("❌ Error occurred while claiming points:", error)
  }

  console.log("=== END VIDEO POINTS CHECK ===")
}
*/

/*
// Event listeners for download and quiz links (unchanged)
document.addEventListener("DOMContentLoaded", () => {
  const allLinks = document.querySelectorAll("a")
  allLinks.forEach((link) => {
    link.addEventListener("click", async () => {
      const href = link.getAttribute("href") || ""
      if (href.includes("/wp-content/uploads/")) {
        console.log("✅ Download link clicked, preparing to send activity POST...")
        let ownerId = "0"
        if (typeof characterData === "object" && characterData.ownerId) {
          ownerId = characterData.ownerId
        } else {
          console.warn("⚠️ Could not find ownerId from characterData")
        }
        const currentUrl = window.location.href
        const AJAX_URL = window.PHPData?.ajaxUrl || "/wp-admin/admin-ajax.php"
        console.log("Owner ID:", ownerId)
        console.log("Current URL:", currentUrl)
        console.log("AJAX URL:", AJAX_URL)
        try {
          const formData = new FormData()
          formData.append("action", "aiovg_claim_video_points")
          formData.append("ownerId", ownerId)
          formData.append("videoUrl", currentUrl)
          const response = await fetch(AJAX_URL, {
            method: "POST",
            body: formData,
          })
          const result = await response.json()
          console.log("📥 Server response:", result)
          if (result.success) {
            if (result.newPoints !== undefined) {
              const pointsEarned = result.newPoints - currentPoints
              currentPoints = result.newPoints
              console.log(`🎉 Points earned: +${pointsEarned}`)
              console.log("Message:", result.message)
            } else {
              console.log("⚠️ No new points awarded:", result.message)
            }
          } else {
            console.log("❌ Failed to claim activity points:", result.message)
          }
        } catch (error) {
          console.error("❌ Error occurred while sending activity POST:", error)
        }
        console.log("=== END DOWNLOAD POINTS CLAIM ===")
      }
    })
  })
})
*/

/*
document.addEventListener("DOMContentLoaded", () => {
  const quizLink = document.querySelector('a.aiovg-link-title.lfk-activities-counter[data-title="Quiz"]')
  if (quizLink) {
    quizLink.addEventListener("click", async (event) => {
      event.preventDefault()
      console.log("✅ 'Quiz' link clicked, preparing to send activity POST...")
      let ownerId = "0"
      if (typeof characterData === "object" && characterData.ownerId) {
        ownerId = characterData.ownerId
      } else {
        console.warn("⚠️ Could not find ownerId from characterData")
      }
      const currentUrl = window.location.href
      const AJAX_URL = window.PHPData?.ajaxUrl || "/wp-admin/admin-ajax.php"
      console.log("Owner ID:", ownerId)
      console.log("Current URL:", currentUrl)
      console.log("AJAX URL:", AJAX_URL)
      try {
        const formData = new FormData()
        formData.append("action", "aiovg_claim_video_points")
        formData.append("ownerId", ownerId)
        formData.append("videoUrl", currentUrl)
        const response = await fetch(AJAX_URL, {
          method: "POST",
          body: formData,
        })
        const result = await response.json()
        console.log("📥 Server response:", result)
        if (result.success) {
          if (typeof result.newPoints !== "undefined") {
            const pointsEarned = result.newPoints - (window.currentPoints || 0)
            window.currentPoints = result.newPoints
            console.log(`🎉 Points earned: +${pointsEarned}`)
            console.log("Message:", result.message)
          } else {
            console.log("⚠️ No new points awarded:", result.message)
          }
        } else {
          console.log("❌ Failed to claim activity points:", result.message)
        }
      } catch (error) {
        console.error("❌ Error occurred while sending activity POST:", error)
      }
      console.log("=== END QUIZ POINTS CLAIM ===")
    })
  } else {
    console.warn("⚠️ No 'Quiz' link found on this page.")
  }
})
*/

function displayUnlockedItemsCategory() {
  if (boughtItems.length === 0) {
    itemsContainer.innerHTML = `
      <div style="text-align: center; color: #666; padding: 40px;">
        <h3>No unlocked items yet!</h3>
        <p>Trade and unlock fun items to see them here!</p>
      </div>
    `
    return
  }

  const container = document.createElement("div")
  container.className = "unlocked-items-main-container"
  container.style.cssText = `
    padding: 20px;
  `

  const itemsByCategory = {
    headwear: [],
    tops: [],
    shoes: [],
    facewear: [],
    bottoms: [],
  }

  boughtItems.forEach((imageUrl) => {
    // Primary: derive category from the folder name in the Firebase URL
    // e.g. .../o/assets%2Faccessories%2FItem.png → folder "accessories" → "facewear"
    let foundCategory = null
    try {
      const decoded = decodeURIComponent(imageUrl)
      const match = decoded.match(/\/o\/(?:assets\/)?([^/?]+)\/[^/?]+/)
      if (match) {
        const folderName = match[1].toLowerCase()
        for (const [categoryKey, folders] of Object.entries(categories)) {
          if (categoryKey === "unlocked") continue
          if (folders.includes(folderName)) {
            foundCategory = categoryKey
            break
          }
        }
      }
    } catch (e) {}

    // Fallback: search allImages (works after images are loaded into memory)
    if (!foundCategory) {
      for (const [categoryKey, folders] of Object.entries(categories)) {
        if (categoryKey === "unlocked") continue
        let categoryImages = []
        folders.forEach((folder) => {
          if (allImages[folder]) categoryImages = categoryImages.concat(allImages[folder])
        })
        if (categoryImages.includes(imageUrl)) {
          foundCategory = categoryKey
          break
        }
      }
    }

    if (foundCategory && itemsByCategory[foundCategory]) {
      itemsByCategory[foundCategory].push(imageUrl)
    }
  })

  const categoryNames = {
    headwear: "Headwear",
    tops: "Tops",
    shoes: "Shoes",
    facewear: "Facewear",
    bottoms: "Bottoms",
  }

  Object.keys(itemsByCategory).forEach((categoryKey) => {
    const categoryItems = itemsByCategory[categoryKey]
    if (categoryItems.length === 0) return

    const categorySection = document.createElement("div")
    categorySection.className = "unlocked-category-section"
    categorySection.style.cssText = `
      margin-bottom: 30px;
    `

    const categoryTitle = document.createElement("h3")
    categoryTitle.textContent = `${categoryNames[categoryKey]} (${categoryItems.length})`
    categoryTitle.style.cssText = `
      color: #333;
      font-size: 18px;
      margin-bottom: 15px;
      padding: 12px 16px;
    background: #e66238; 
    color: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(249, 144, 0, 0.3);
    `
    categorySection.appendChild(categoryTitle)

    const categoryGrid = document.createElement("div")
    categoryGrid.className = "items-grid"
    categoryGrid.style.cssText = `
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    `

    categoryItems.forEach((imageUrl, index) => {
      const itemCard = document.createElement("div")
      itemCard.className = "item-card unlocked-item-card"
      itemCard.dataset.imageUrl = imageUrl
      itemCard.dataset.category = categoryKey

      const isSelected = selectedItems[categoryKey] === imageUrl
      if (isSelected) {
        itemCard.classList.add("selected")
      }

      itemCard.style.cssText = `
        position: relative;
        border: 2px solid ${isSelected ? "#f90" : "#ddd"};
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      `

      const itemPreviewContainer = document.createElement("div")
      itemPreviewContainer.className = "item-preview-container"
      const itemPreviewBg = document.createElement("div")
      itemPreviewBg.className = "item-preview-background"
      itemPreviewContainer.appendChild(itemPreviewBg)
      itemCard.appendChild(itemPreviewContainer)

      const img = document.createElement("img")
      img.src = imageUrl
      img.className = "item-image"
      img.alt = `${categoryNames[categoryKey]} ${index + 1}`
      itemCard.appendChild(img)

      const equipBtn = document.createElement("button")
      equipBtn.className = "equip-btn"
      equipBtn.textContent = isSelected ? "Active" : "Equip"
      equipBtn.style.cssText = `
  background: ${isSelected ? "#1e1e1e" : "#f90"};
  color: ${isSelected ? "white" : "#1e1e1e"};
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  transition: background 0.3s ease;
`

      if (isSelected) {
        equipBtn.classList.add("unequip")
      }

      equipBtn.addEventListener("click", (e) => {
        e.stopPropagation()
        handleItemSelection(imageUrl, categoryKey, false)
      })

      itemCard.appendChild(equipBtn)

      itemCard.addEventListener("mouseenter", () => {
        itemCard.style.transform = "translateY(-2px)"
        itemCard.style.boxShadow = "0 8px 20px rgba(0,0,0,0.15)"
      })

      itemCard.addEventListener("mouseleave", () => {
        itemCard.style.transform = "translateY(0)"
        itemCard.style.boxShadow = "0 4px 12px rgba(0,0,0,0.1)"
      })

      itemCard.addEventListener("click", (e) => {
        // Only trigger if the click wasn't on the equip button itself
        if (!e.target.classList.contains("equip-btn")) {
          handleItemSelection(imageUrl, categoryKey, false)
        }
      })

      categoryGrid.appendChild(itemCard)
    })

    categorySection.appendChild(categoryGrid)
    container.appendChild(categorySection)
  })

  itemsContainer.innerHTML = ""
  itemsContainer.appendChild(container)
}

async function initializeAppWithCharacterData() {
  // Initialize cache system
  initializeCache()
  
  currentOwnerId = phpData.ownerIdFromPHP || null
  characterData = phpData.characterDataFromPHP || null

  console.log("🔧 Initializing app with character data...")
  console.log("Owner ID:", currentOwnerId)
  console.log("Character Data:", characterData)

  const firebaseInitialized = await initializeFirebase()
  if (firebaseInitialized) {
    await fetchAssetsFromFirestore()
  }

  if (currentOwnerId !== null && characterData !== null) {
    currentPoints = characterData.points || 0
    boughtItems = characterData.boughtItems || []
    selectedItems = {
      headwear: characterData.selectedHeadUrl || null,
      tops: characterData.selectedTopUrl || null,
      bottoms: characterData.selectedBottomUrl || null,
      shoes: characterData.selectedShoesUrl || null,
      facewear: characterData.selectedAccessoryUrl || null,
    }
    selectedLanguage = characterData.selectedLanguage || "en"

    Object.keys(selectedItems).forEach((key) => {
      if (!selectedItems[key]) {
        delete selectedItems[key]
      }
    })

    if (userCreditsEl) updateCreditsDisplay()
    if (avatarCharacterEl) updateAvatarDisplay()
    if (unlockedItemsGrid) updateUnlockedItemsDisplay()

    // Main container is already shown - just update content
    // Display headwear category immediately (even if assets aren't loaded yet)
    if (itemsContainer && categoryTitleEl) {
      displayCategory(currentCategory) // This will show loading state initially
    }

    // Load all images in background and update display when ready
    if (firebaseInitialized && !isAssetsLoaded) {
      loadAllImages().then(() => {
        organizeBoughtItemsByCategory()
        displayCategory(currentCategory) // Refresh display with loaded assets
      })
    } else if (isAssetsLoaded) {
      // Assets already loaded, just display the default category
      console.log("🎯 Assets already loaded - displaying default category:", currentCategory)
      organizeBoughtItemsByCategory()
      displayCategory(currentCategory)
    }

    if (languageModalOverlay && (!characterData.selectedLanguage || characterData.selectedLanguage === "en")) {
      openLanguageModal()
    }

    // NEW: Check for avatar-dress-up page and log 1080 activity
    const currentPath = window.location.pathname
    if (currentPath.includes("/avatar-dress-up")) {
      await log1080Activity()
    }

    // Auto-claim points functionality is currently disabled
    // if (isVideoPage) {
    //   await autoClaimVideoPoints()
    // } else if (isActivityPage) {
    //   await autoClaimActivityPoints()
    // }
  } else {
    // Handle case when no character is found
    if (messageContainer && messageTitle && messageText && mainContainer) {
      messageContainer.classList.add("show")
      messageTitle.textContent = "No Character Found"
      messageText.textContent = "Please set a valid barcode in your session to load a character."
      mainContainer.classList.remove("show")
    }

    // Still show a placeholder in items container
    if (itemsContainer) {
      itemsContainer.innerHTML = `
        <div style="text-align: center; color: #666; padding: 40px; background: #f8f9fa; border-radius: 8px; margin: 20px;">
          <h3>Character Required</h3>
          <p>A valid character is needed to access avatar items.</p>
        </div>
      `
    }

    console.log("No character found, but checking for video/activity page anyway...")

    // NEW: Still check for avatar-dress-up page even without character
    const currentPath = window.location.pathname
    if (currentPath.includes("/avatar-dress-up")) {
      await log1080Activity()
    }

    // Auto-claim points functionality is currently disabled
    // if (isVideoPage) {
    //   await autoClaimVideoPoints()
    // } else if (isActivityPage) {
    //   await autoClaimActivityPoints()
    // }
  }
}

function updateCreditsDisplay() {
  userCreditsEl.textContent = currentPoints
}

function updateUnlockedCount() {
  const count = Object.keys(selectedItems).length
  unlockedCountEl.textContent = count
}

// MODIFIED: Enhanced loadImagesFromFolder with lazy loading and animations
async function loadImagesFromFolder(folderPath, onItemLoaded = null) {
  try {
    console.log(`🔍 Loading images from folder: ${folderPath}`)

    // Check cache first
    const cachedImages = CacheManager.getCachedImages(folderPath)
    if (cachedImages) {
      console.log(`✅ Using cached images for ${folderPath}`)
      if (onItemLoaded) {
        cachedImages.forEach((url, index) => {
          onItemLoaded(url, index, cachedImages.length, true) // true = from cache
        })
      }
      return cachedImages
    }

    console.log(`🌐 Fetching images from Firebase for ${folderPath}`)
    const folderRef = ref(storage, folderPath)
    const result = await listAll(folderRef)
    const urls = []

    // Load items in batches of 20 for better performance
    const batchSize = 20
    for (let i = 0; i < result.items.length; i += batchSize) {
      const batch = result.items.slice(i, i + batchSize)

      const batchPromises = batch.map(async (itemRef, batchIndex) => {
        try {
          const url = await getDownloadURL(itemRef)
          const globalIndex = i + batchIndex

          // Notify when item is loaded (for animations)
          if (onItemLoaded) {
            onItemLoaded(url, globalIndex, result.items.length, false) // false = from Firebase
          }

          return url
        } catch (error) {
          console.error(`Failed to get download URL for ${itemRef.name}:`, error)
          return null
        }
      })

      const batchUrls = await Promise.all(batchPromises)
      const validBatchUrls = batchUrls.filter((url) => url !== null)
      urls.push(...validBatchUrls)

      // Removed artificial delay for faster loading
    }

    // Cache the results
    if (urls.length > 0) {
      CacheManager.setCachedImages(folderPath, urls)
      console.log(`💾 Cached ${urls.length} images for ${folderPath}`)
    }

    return urls
  } catch (error) {
    console.error(`❌ Error loading images from ${folderPath}:`, error)
    return []
  }
}

// MODIFIED: Load specific category with lazy loading support
async function loadCategoryImages(category, onItemLoaded = null) {
  if (!storage) {
    console.error("❌ Firebase Storage not initialized")
    return []
  }

  const categoryFolders = categories[category] || [category]
  let categoryImages = []

  console.log(`🚀 Loading images for category: ${category}`)

  try {
    // Load from assets folder
    const assetsRef = ref(storage, "assets")
    const assetsFolders = await listAll(assetsRef)

    for (const folderRef of assetsFolders.prefixes) {
      const folderName = folderRef.name.toLowerCase()
      if (categoryFolders.includes(folderName)) {
        const folderPath = `assets/${folderName}`
        const images = await loadImagesFromFolder(folderPath, onItemLoaded)
        if (images.length > 0) {
          if (!allImages[folderName]) {
            allImages[folderName] = []
          }
          allImages[folderName] = images
          categoryImages = categoryImages.concat(images)
          console.log(`✅ Loaded ${images.length} images from ${folderPath}`)
        }
      }
    }

    // Load from root folders
    const rootRef = ref(storage, "")
    const rootFolders = await listAll(rootRef)

    for (const folderRef of rootFolders.prefixes) {
      const folderName = folderRef.name.toLowerCase()
      if (folderName !== "assets" && categoryFolders.includes(folderName)) {
        const images = await loadImagesFromFolder(folderName, onItemLoaded)
        if (images.length > 0) {
          if (!allImages[folderName]) {
            allImages[folderName] = []
          }
          allImages[folderName] = allImages[folderName].concat(images)
          categoryImages = categoryImages.concat(images)
          console.log(`✅ Loaded ${images.length} images from ${folderName}`)
        }
      }
    }

    // Handle accessories special case for facewear and tops
    if (category === "facewear" || category === "tops") {
      const accessoriesImages = await loadImagesFromFolder("accessories", onItemLoaded)
      if (accessoriesImages.length > 0) {
        if (!allImages["accessories"]) {
          allImages["accessories"] = accessoriesImages
        }
        
        const ties = accessoriesImages.filter((url) => url.toLowerCase().includes("tie"))
        const remainingAccessories = accessoriesImages.filter((url) => !url.toLowerCase().includes("tie"))

        if (category === "tops") {
          categoryImages = categoryImages.concat(ties)
          if (!allImages["tops"]) {
            allImages["tops"] = []
          }
          allImages["tops"] = allImages["tops"].concat(ties)
        }

        if (category === "facewear") {
          categoryImages = categoryImages.concat(remainingAccessories)
          if (!allImages["facewear"]) {
            allImages["facewear"] = []
          }
          allImages["facewear"] = allImages["facewear"].concat(remainingAccessories)
        }
      }
    }

    console.log(`✅ Category ${category} loaded with ${categoryImages.length} items`)
    return categoryImages
  } catch (error) {
    console.error(`❌ Error loading category ${category}:`, error)
    return []
  }
}

// MODIFIED: Simplified loadAllImages for background loading
async function loadAllImages() {
  if (!storage) {
    console.error("❌ Firebase Storage not initialized")
    return
  }

  try {
    console.log("🚀 Starting to load all images in background (parallel loading)...")

    // Load from assets folder
    const assetsRef = ref(storage, "assets")
    const assetsFolders = await listAll(assetsRef)

    // Load all asset folders in parallel
    const assetPromises = assetsFolders.prefixes.map(async (folderRef) => {
      const folderName = folderRef.name.toLowerCase()
      const folderPath = `assets/${folderName}`
      const images = await loadImagesFromFolder(folderPath)
      if (images.length > 0) {
        allImages[folderName] = images
        console.log(`✅ Background loaded ${images.length} images from ${folderPath}`)
      }
    })

    // Load from root folders
    const rootRef = ref(storage, "")
    const rootFolders = await listAll(rootRef)

    // Load all root folders in parallel
    const rootPromises = rootFolders.prefixes.map(async (folderRef) => {
      const folderName = folderRef.name.toLowerCase()
      if (folderName !== "assets") {
        const images = await loadImagesFromFolder(folderName)
        if (images.length > 0) {
          if (!allImages[folderName]) {
            allImages[folderName] = []
          }
          allImages[folderName] = allImages[folderName].concat(images)
          console.log(`✅ Background loaded ${images.length} images from ${folderName}`)
        }
      }
    })

    // Wait for all folders to load in parallel
    await Promise.all([...assetPromises, ...rootPromises])

    // Process accessories folder
    const allAccessories = allImages["accessories"] || []
    const ties = allAccessories.filter((url) => url.toLowerCase().includes("tie"))
    const remainingAccessories = allAccessories.filter((url) => !url.toLowerCase().includes("tie"))

    if (!allImages["tops"]) {
      allImages["tops"] = []
    }
    allImages["tops"] = allImages["tops"].concat(ties)

    if (!allImages["facewear"]) {
      allImages["facewear"] = []
    }
    allImages["facewear"] = allImages["facewear"].concat(remainingAccessories)

    delete allImages["accessories"]

    isAssetsLoaded = true
    isLoading = false

    console.log("✅ All background images loaded successfully!")
    console.log("📊 Final image counts by folder:", 
      Object.keys(allImages).map((key) => `${key}: ${allImages[key].length}`)
    )
  } catch (error) {
    console.error("❌ Error loading background images:", error)
    isLoading = false
  }
}

function displayCategory(category) {
  const categoryNames = {
    headwear: "Select Headwear",
    tops: "Select Tops",
    shoes: "Select Shoes",
    facewear: "Select Facewear",
    bottoms: "Select Bottoms",
    unlocked: "Unlocked Items",
  }

  if (categoryTitleEl) {
    categoryTitleEl.textContent = categoryNames[category] || "Select Items"
  }

  if (category === "unlocked") {
    displayUnlockedItemsCategory()
    return
  }

  // Check if category images are already loaded
  const categoryFolders = categories[category] || [category]
  let categoryImages = []
  let hasAllImages = true

  categoryFolders.forEach((folder) => {
    if (allImages[folder]) {
      categoryImages = categoryImages.concat(allImages[folder])
    } else {
      hasAllImages = false
    }
  })

  // Special handling for accessories split
  if (category === "facewear" && allImages["facewear"]) {
    categoryImages = categoryImages.concat(allImages["facewear"])
  } else if (category === "tops" && allImages["tops"]) {
    categoryImages = categoryImages.concat(allImages["tops"])
  }

  // Create grid container
  const grid = document.createElement("div")
  grid.className = "items-grid"
  grid.style.cssText = `
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    padding: 20px;
  `

  // Add animation styles for lazy loading
  const style = document.createElement('style')
  style.id = 'lazy-loading-styles'
  style.textContent = `
    .item-card.lazy-load {
      opacity: 0;
      transform: translateY(20px) scale(0.9);
      animation: lazyItemAppear 0.4s ease forwards;
    }
    @keyframes lazyItemAppear {
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
  `
  if (!document.getElementById('lazy-loading-styles')) {
    document.head.appendChild(style)
  }

  // Clear container and add grid
  if (itemsContainer) {
    itemsContainer.innerHTML = ""
    itemsContainer.appendChild(grid)
  }

  if (hasAllImages && categoryImages.length > 0) {
    // Images are cached - display instantly without animations
    console.log(`📦 Displaying ${categoryImages.length} cached items for ${category}`)
    displayCategoryItems(categoryImages, category, grid, false) // false = no animations for cached
  } else {
    // Images need to be loaded - show loading and add items with animations
    console.log(`🌐 Loading ${category} category with lazy loading...`)
    
    // Load category with lazy loading and animations
    loadCategoryImages(category, (imageUrl, index, total, fromCache) => {
      // Remove loading placeholder on first item
      if (index === 0) {
        grid.innerHTML = ''
      }
      
      // Add item with animation delay (only if not from cache)
      const delay = fromCache ? 0 : index * 20 // 20ms delay between items for faster Firebase loading
      setTimeout(() => {
        addItemToGrid(imageUrl, category, grid, !fromCache) // animate only if not from cache
      }, delay)
    }).then(() => {
      console.log(`✅ ${category} category loading complete`)
    })
  }
}

// Helper function to display category items
function displayCategoryItems(categoryImages, category, grid, shouldAnimate = false) {
  const itemPrice = categoryPrices[category] || 5
  categoryImages.forEach((imageUrl, index) => {
    const delay = shouldAnimate ? index * 30 : 0 // 30ms delay for animations
    setTimeout(() => {
      addItemToGrid(imageUrl, category, grid, shouldAnimate)
    }, delay)
  })
}

// Helper function to add individual item to grid
function addItemToGrid(imageUrl, category, grid, shouldAnimate = false) {
  const itemPrice = categoryPrices[category] || 5
  const itemCard = document.createElement("div")
  itemCard.className = shouldAnimate ? "item-card lazy-load" : "item-card"
  itemCard.dataset.imageUrl = imageUrl
  itemCard.dataset.category = category

  const isOwned = boughtItems.includes(imageUrl)
  const isSelected = selectedItems[category] === imageUrl

  if (isSelected) {
    itemCard.classList.add("selected")
  }

  itemCard.style.cssText += `
    position: relative;
    border: 2px solid ${isSelected ? "#f90" : "#ddd"};
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  `

  // Add animation delay for staggered effect
  if (shouldAnimate) {
    itemCard.style.animationDelay = `${Math.random() * 0.3}s`
  }

  const itemPreviewContainer = document.createElement("div")
  itemPreviewContainer.className = "item-preview-container"
  const itemPreviewBg = document.createElement("div")
  itemPreviewBg.className = "item-preview-background"
  itemPreviewContainer.appendChild(itemPreviewBg)
  itemCard.appendChild(itemPreviewContainer)

  const img = document.createElement("img")
  img.src = imageUrl
  img.className = `item-image${category === "shoes" ? " item-image--shoes" : ""}`
  img.alt = `${category} item`
  img.loading = 'lazy'
  img.onerror = () => itemCard.remove()
  itemCard.appendChild(img)

  if (!isOwned) {
    const lockIcon = document.createElement("div")
    lockIcon.className = "simple-lock-icon"
    lockIcon.innerHTML = "<img src='https://lote4kids.com/wp-content/uploads/2025/09/avatar-lock-icon.png' alt='Lock Icon' style='width:36px; height:36px; object-fit:contain;' />"
    lockIcon.style.cssText = `
      position: absolute;
      top: 60%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 36px;
      z-index: 10;
      pointer-events: none;
      opacity: 0.7;
    `
    itemCard.appendChild(lockIcon)

    const purchaseBtn = document.createElement("button")
    purchaseBtn.className = "purchase-btn"
    purchaseBtn.innerHTML = `${itemPrice}<img src="https://lote4kids.com/wp-content/uploads/2025/07/IMG_7049.png" alt="Feather" style="width: 16px; height: 16px; margin-left: 8px;">`

    purchaseBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      if (currentPoints < itemPrice) {
        showInsufficientFundsModal(category)
      } else {
        showPurchaseConfirmationModal(imageUrl, category)
      }
    })

    itemCard.appendChild(purchaseBtn)

    itemCard.addEventListener("click", (e) => {
      if (!e.target.classList.contains("purchase-btn") && !purchaseBtn.contains(e.target)) {
        if (currentPoints < itemPrice) {
          showInsufficientFundsModal(category)
        } else {
          showPurchaseConfirmationModal(imageUrl, category)
        }
      }
    })
  } else {
    const actionBtn = document.createElement("button")
    actionBtn.className = "equip-btn"
    actionBtn.textContent = isSelected ? "Active" : "Equip"
    actionBtn.style.cssText = `
      background: ${isSelected ? "#1e1e1e" : "#f90"};
      color: ${isSelected ? "white" : "#1e1e1e"};
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 12px;
      cursor: pointer;
      transition: background 0.3s ease;
    `

    actionBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      handleItemSelection(imageUrl, category, false)
    })

    itemCard.appendChild(actionBtn)

    itemCard.addEventListener("click", (e) => {
      if (!e.target.classList.contains("equip-btn")) {
        handleItemSelection(imageUrl, category, false)
      }
    })
  }

  itemCard.addEventListener("mouseenter", () => {
    itemCard.style.transform = "translateY(-5px)"
    itemCard.style.boxShadow = "0 8px 25px rgba(0,0,0,0.15)"
  })

  itemCard.addEventListener("mouseleave", () => {
    itemCard.style.transform = "translateY(0)"
    itemCard.style.boxShadow = "0 4px 12px rgba(0,0,0,0.1)"
  })

  grid.appendChild(itemCard)
}

function updateAvatarDisplay() {
  avatarCharacterEl.querySelectorAll(".avatar-item-layer").forEach((layer) => layer.remove())

  const zIndexMap = {
    bottoms: 1,
    tops: 2,
    shoes: 3,
    headwear: 4,
    facewear: 5,
  }

  for (const category in selectedItems) {
    if (selectedItems[category]) {
      const itemLayer = document.createElement("div")
      itemLayer.className = "avatar-item-layer"
      itemLayer.style.backgroundImage = `url('${selectedItems[category]}')`
      itemLayer.style.zIndex = zIndexMap[category] || 0
      avatarCharacterEl.appendChild(itemLayer)
    }
  }
}

function updateUnlockedItemsDisplay() {
  unlockedItemsGrid.innerHTML = ""

  const displayOrder = ["headwear", "tops", "shoes", "facewear", "bottoms"]
  displayOrder.forEach((category) => {
    const imageUrl = selectedItems[category]
    if (imageUrl) {
      const unlockedItem = document.createElement("div")
      unlockedItem.className = "unlocked-item"
      unlockedItem.style.backgroundImage = `url('${imageUrl}')`
      unlockedItem.dataset.category = category
      unlockedItem.dataset.imageUrl = imageUrl

      const removeBtn = document.createElement("button")
      removeBtn.className = "remove-item-btn"
      removeBtn.innerHTML = "×"
      removeBtn.addEventListener("click", (e) => {
        e.stopPropagation()
        removeSelectedItem(category)
      })

      unlockedItem.appendChild(removeBtn)

      unlockedItem.addEventListener("click", () => {
        document.querySelectorAll(".category-tab").forEach((t) => t.classList.remove("active"))
        const targetTab = document.querySelector(`.category-tab[data-category="${category}"]`)
        if (targetTab) {
          targetTab.classList.add("active")
          currentCategory = category
          displayCategory(currentCategory)
        }
      })

      unlockedItemsGrid.appendChild(unlockedItem)
    }
  })

  updateUnlockedCount()
}

async function handleItemSelection(imageUrl, category, forceEquip = false) {
  const isCurrentlySelected = selectedItems[category] === imageUrl

  if (forceEquip) {
    selectedItems[category] = imageUrl
  } else {
    if (isCurrentlySelected) {
      delete selectedItems[category]
    } else {
      selectedItems[category] = imageUrl
    }
  }

  // Update all item cards in the current category display
  document.querySelectorAll(`.item-card[data-category="${category}"]`).forEach((card) => {
    const cardImageUrl = card.dataset.imageUrl
    const equipBtn = card.querySelector(".equip-btn")
    
    if (selectedItems[category] === cardImageUrl) {
      card.classList.add("selected")
      card.style.border = "2px solid #f90"
      if (equipBtn) {
        equipBtn.textContent = "Active"
        equipBtn.style.background = "#1e1e1e"
        equipBtn.style.color = "white"
        equipBtn.classList.add("unequip")
      }
    } else {
      card.classList.remove("selected")
      card.style.border = "2px solid #ddd"
      if (equipBtn) {
        equipBtn.textContent = "Equip"
        equipBtn.style.background = "#f90"
        equipBtn.style.color = "#1e1e1e"
        equipBtn.classList.remove("unequip")
      }
    }
  })

  // Update unlocked items cards
  document.querySelectorAll(`.unlocked-item-card[data-category="${category}"]`).forEach((card) => {
    const cardImageUrl = card.dataset.imageUrl
    const equipBtn = card.querySelector(".equip-btn")
    if (selectedItems[category] === cardImageUrl) {
      card.classList.add("selected")
      card.style.border = "2px solid #f90"
      if (equipBtn) {
        equipBtn.textContent = "Active"
        equipBtn.style.background = "#1e1e1e"
        equipBtn.style.color = "white"
        equipBtn.classList.add("unequip")
      }
    } else {
      card.classList.remove("selected")
      card.style.border = "2px solid #ddd"
      if (equipBtn) {
        equipBtn.textContent = "Equip"
        equipBtn.style.background = "#f90"
        equipBtn.style.color = "#1e1e1e"
        equipBtn.classList.remove("unequip")
      }
    }
  })

  updateAvatarDisplay()
  updateUnlockedItemsDisplay()
  await saveAvatar()
}

async function removeSelectedItem(category) {
  const imageUrlToRemove = selectedItems[category]
  if (imageUrlToRemove) {
    await handleItemSelection(imageUrlToRemove, category, false)
  }
}

async function saveAvatar() {
  if (!currentOwnerId) {
    showNotification("No owner ID set", "error")
    return
  }

  try {
    const formData = new FormData()
    formData.append("action", "save_avatar")
    formData.append("ownerId", currentOwnerId)

    const selections = {
      headwear: selectedItems.headwear || "",
      tops: selectedItems.tops || "",
      bottoms: selectedItems.bottoms || "",
      shoes: selectedItems.shoes || "",
      facewear: selectedItems.facewear || "",
    }

    Object.keys(selections).forEach((key) => {
      formData.append(`selections[${key}]`, selections[key])
    })

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()

    if (result.success) {
      showNotification("Avatar saved successfully!")
      // Sync phpData and refresh sidebar avatar
      if (window.phpData && window.phpData.characterDataFromPHP) {
        window.phpData.characterDataFromPHP.selectedHeadUrl       = selectedItems.headwear || ""
        window.phpData.characterDataFromPHP.selectedTopUrl        = selectedItems.tops    || ""
        window.phpData.characterDataFromPHP.selectedBottomUrl     = selectedItems.bottoms || ""
        window.phpData.characterDataFromPHP.selectedShoesUrl      = selectedItems.shoes   || ""
        window.phpData.characterDataFromPHP.selectedAccessoryUrl  = selectedItems.facewear || ""
      }
      updateSidebarAvatar()
    } else {
      showNotification(result.message || "Save failed", "error")
    }
  } catch (error) {
    console.error("Save error:", error)
    showNotification("Save failed", "error")
  }
}

function openLanguageModal() {
  languageModalOverlay.classList.add("show")
  languageOptionButtons.forEach((button) => {
    button.classList.remove("selected")
    if (button.dataset.lang === selectedLanguage) {
      button.classList.add("selected")
    }
  })
}

function closeLanguageModal() {
  languageModalOverlay.classList.remove("show")
}

async function saveLanguage(languageCode) {
  if (!currentOwnerId) {
    showNotification("No owner ID set", "error")
    return
  }

  try {
    const formData = new FormData()
    formData.append("action", "save_language")
    formData.append("ownerId", currentOwnerId)
    formData.append("languageCode", languageCode)

    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()

    if (result.success) {
      selectedLanguage = result.selectedLanguage
      showNotification(`Language set to ${languageCode.toUpperCase()}!`)
      closeLanguageModal()
    } else {
      showNotification(result.message || "Failed to save language", "error")
    }
  } catch (error) {
    console.error("Save language error:", error)
    showNotification("Failed to save language", "error")
  }
}

// Event Listeners
if (document.querySelectorAll(".category-tab").length > 0) {
  document.querySelectorAll(".category-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      document.querySelectorAll(".category-tab").forEach((t) => t.classList.remove("active"))
      tab.classList.add("active")
      currentCategory = tab.dataset.category
      displayCategory(currentCategory)
    })
  })
  
  // Set default tab as active (headwear)
  const defaultTab = document.querySelector(`.category-tab[data-category="${currentCategory}"]`)
  if (defaultTab) {
    defaultTab.classList.add("active")
    console.log("🎯 Set default tab active:", currentCategory)
  }
}

if (saveAvatarBtn) {
  saveAvatarBtn.addEventListener("click", saveAvatar)
}

if (closeLanguageModalBtn) {
  closeLanguageModalBtn.addEventListener("click", closeLanguageModal)
}

if (languageOptionButtons.length > 0) {
  languageOptionButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const lang = button.dataset.lang
      saveLanguage(lang)
    })
  })
}


// Add cache management functions to window for debugging
window.CacheManager = CacheManager
window.clearImageCache = () => {
  CacheManager.clearAllCaches()
  console.log("🗑️ All image caches cleared!")
}
window.getCacheStats = () => {
  const stats = CacheManager.getCacheStats()
  console.log("📊 Cache Statistics:", stats)
  return stats
}

// Initialize the app
initializeAppWithCharacterData()

const HAT_LAYERS = [
  "Wzard%20Ht.png",
  "Party%20Hat%20(3).png",
  "Chefs.png",
  "Fire%20Fighter.png"
];

function elevateHats() {
  HAT_LAYERS.forEach(hat => {
    const hatImg = document.querySelector(`img[data-character-layer="selectedHeadUrl"][src*="${hat}"]`);
    if (hatImg) {
      let marginTop = (hat === "Wzard%20Ht.png") ? "-65px" : "-35px";
      hatImg.style.setProperty("margin-top", marginTop, "important");
    }
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", elevateHats);
} else {
  elevateHats();
}
 
const avatarObserver = new MutationObserver(elevateHats);
avatarObserver.observe(document.body, { childList: true, subtree: true });

// avatar.js
if (typeof window.jQuery !== 'undefined') {
  window.jQuery(function($){
    console.log('avatar.js loaded!');
  });
}

// Show loading overlay initially
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const mainContainer = document.getElementById('mainContainer');

    // Show loading for 1 second
    setTimeout(function() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hide');
        }
        if (mainContainer) {
            mainContainer.classList.add('show');
        }
        document.body.classList.add('loading-complete');

        // Remove loading overlay from DOM after transition
        setTimeout(function() {
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }, 500);
    }, 500);
});

function showInsufficientFundsMessage() {
    alert("You don't have enough feathers to unlock this item.");
}

document.addEventListener('DOMContentLoaded', function() {
    const bookCountElement = document.getElementById('bookCount');

    if (bookCountElement && window.phpData && typeof window.phpData.rewardCount !== 'undefined') {
        bookCountElement.textContent = window.phpData.rewardCount;
    }

    // Listen for book count updates
    document.addEventListener('bookCountUpdated', function(e) {
        if (bookCountElement && e.detail && typeof e.detail.bookCount !== 'undefined') {
            bookCountElement.textContent = e.detail.bookCount;
        }
    });

    // Update feather display when points are earned from book/activity visits
    document.addEventListener('bookVisitLogged', function(e) {
        if (e.detail && e.detail.newPoints !== undefined) {
            currentPoints = e.detail.newPoints;
            if (userCreditsEl) updateCreditsDisplay();
        }
    });
    document.addEventListener('activityVisitLogged', function(e) {
        if (e.detail && e.detail.newPoints !== undefined) {
            currentPoints = e.detail.newPoints;
            if (userCreditsEl) updateCreditsDisplay();
        }
    });
});

// Sidebar avatar display
function updateSidebarAvatar() {
    const wrap = document.getElementById('sidebar-avatar-wrap');
    if (!wrap) return;

    // Remove old layers
    wrap.querySelectorAll('.sidebar-avatar-layer').forEach(el => el.remove());

    const charData = window.phpData && window.phpData.characterDataFromPHP;
    if (!charData) return;

    const layers = [
        { key: 'selectedBottomUrl',    z: 1 },
        { key: 'selectedShoesUrl',     z: 2 },
        { key: 'selectedTopUrl',       z: 3 },
        { key: 'selectedHeadUrl',      z: 4 },
        { key: 'selectedAccessoryUrl', z: 5 },
    ];

    layers.forEach(function(layer) {
        const url = charData[layer.key];
        if (url) {
            const div = document.createElement('div');
            div.className = 'sidebar-avatar-layer';
            div.style.backgroundImage = "url('" + url + "')";
            div.style.zIndex = layer.z;
            wrap.appendChild(div);
        }
    });
}

document.addEventListener('DOMContentLoaded', updateSidebarAvatar);

const HAT_ASSETS = [
    "Wzard%20Ht.png", // Wizard Hat
    "Party%20Hat%20(3).png", // Party Hat
    "Chefs.png", // Chef's Hat
    "Fire%20Fighter.png" // Fire Fighter Hat
]

function applyHatStyling() {
    const avatarLayers = document.querySelectorAll(".avatar-item-layer")

    avatarLayers.forEach((layer) => {
        const backgroundImage = layer.style.backgroundImage

        const matchedHat = HAT_ASSETS.find(hat => backgroundImage.includes(hat))

        if (matchedHat) {
            if (matchedHat.includes("Wzard%20Ht.png")) {
                layer.style.marginTop = "-40px"
            } else {
                layer.style.marginTop = "-20px"
            }

            console.log("[v0] Hat styling applied:", matchedHat)
        }
    })
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", applyHatStyling)
} else {
    applyHatStyling()
}

const observer = new MutationObserver(() => {
    applyHatStyling()
})

const avatarCharacter = document.getElementById("avatarCharacter")
if (avatarCharacter) {
    observer.observe(avatarCharacter, {
        childList: true,
        attributes: true,
        subtree: true,
    })
}

const MODAL_HAT_ASSETS = [
    "Wzard%20Ht.png",
    "Party%20Hat%20(3).png",
    "Chefs.png",
    "Fire%20Fighter.png"
];

function applyModalHatStyling() {
    const exactModalBox = document.querySelector(
        'div[style*="width: 200px"][style*="height: 200px"][style*="overflow: hidden"][style*="position: relative"]'
    );
    if (exactModalBox) {
        exactModalBox.style.height = "300px";
        exactModalBox.style.marginBottom = "-10px";

        const modalLayers = exactModalBox.querySelectorAll('div[style*="background-image"]');
        modalLayers.forEach(layer => {
            const style = layer.getAttribute("style");
            const matchedHat = MODAL_HAT_ASSETS.find(hat => style.includes(hat));
            if (matchedHat) {
                const newMargin = matchedHat.includes("Wzard%20Ht.png") ? "-40px" : "-15px";
                layer.style.marginTop = newMargin;
            }
        });
    }

    const buttonDivs = document.querySelectorAll(
        'div[style*="display: flex"][style*="justify-content: center"]'
    );
    buttonDivs.forEach(div => {
        const buttons = div.querySelectorAll("button");
        if (
            buttons.length === 2 &&
            buttons[0].textContent.trim() === "Use it" &&
            buttons[1].textContent.trim() === "Store"
        ) {
            div.style.marginTop = "30px";
        }
    }); 
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", applyModalHatStyling);
} else {
    applyModalHatStyling();
}

const modalObserver = new MutationObserver(applyModalHatStyling);
modalObserver.observe(document.body, {
    childList: true,
    subtree: true
}); 

window.goBackOrHome = function () {
    if (document.referrer && document.referrer !== window.location.href) {
        window.history.back();
    } else {
        window.location.href = "/member-home/";
    }
};