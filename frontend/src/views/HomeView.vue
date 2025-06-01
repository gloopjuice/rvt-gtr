<template>
  <div class="hero">
    <div class="content">
      <h1 class="title">
        JAUNĀ RVTGTR <br />
        VERSIJA IR KLĀT!
      </h1>
      <p class="subtitle">
        KAS JAUNS? ŠAJĀ VERSIJĀ IR JAUNI KLOĶĪŠI KO PAGROZĪT, KAS MAINA SKAŅU!
      </p>
      <img src="/Untitled-1.png" alt="Hero Image" class="hero-image" />

      <button class="cta">Lejupielādēt</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",
};
</script>

<style scoped>
.hero {
  background-color: #373737;
  color: white;
  min-height: 100vh;
  padding: 5vh 5vw;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
  font-family: 'Segoe UI', sans-serif;
}

.content {
  max-width: 50%;
  z-index: 2;
}

.title {
  font-size: 7vh;
  font-weight: 900;
  line-height: 1.1;
  margin-bottom: 1rem;
}

.subtitle {
  font-size: 2.2vh;
  color: #cccccc;
  margin-bottom: 2rem;
  max-width: 90%;
}

.cta {
  background: white;
  color: black;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 999px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cta:hover {
  background: #00ff99;
  color: #000;
}

.hero-image {
  position: absolute;
  right: 5vw;
  top: 50%;
  transform: translateY(-50%);
  max-height: 80vh;
  z-index: 1;
  pointer-events: none;
}

@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    text-align: center;
  }

  .content {
    max-width: 100%;
  }

  .hero-image {
    position: static;
    transform: none;
    max-width: 90%;
    margin-top: 2rem;
  }
}
</style>
