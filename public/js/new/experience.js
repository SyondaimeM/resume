AOS.init();

//  Work experience cards

const experiencecards = document.querySelector(".experience-cards");
const exp = [
  {
    title: "Senior PHP Developer | Backend Engineer",
    cardImage: "images/exp/awareads.png",
    place: "Aware Ads Inc. · Toronto, ON",
    time: "July 2025 – Present",
    responsibilities: [
      "Build and maintain high-concurrency advertising platform services with PHP, Laravel, and Node.js.",
      "Normalize third-party ad network data for real-time bidding, reporting, and analytics.",
      "Develop load-balanced APIs and microservices, and improve live campaign notifications with WebSockets and Redis queues.",
      "Lead sprint planning, code reviews, and pair programming while supporting automated testing and performance monitoring.",
      "Use Docker and Kubernetes to deploy scalable services with geographic and data-residency requirements.",
    ],
  },
  {
    title: "Senior PHP Developer | Full Stack Engineer",
    cardImage: "",
    brand: "VisionTale",
    place: "VisionTale Studio · GTA, ON",
    time: "July 2024 – June 2025",
    responsibilities: [
      "Built and maintained Laravel applications with dynamic React frontends for digital storytelling products.",
      "Prepared normalized, action-ready datasets from raw content.",
      "Improved database retrieval time by 15% through query and database optimization.",
      "Worked with cross-functional teams to ship features that improved user engagement by 20%.",
    ],
  },
  {
    title: "PHP & Node.js Backend Developer",
    cardImage: "",
    place: "RBC PayEdge · Toronto, ON",
    time: "April 2023 – June 2024",
    responsibilities: [
      "Developed and deployed backend modules, microservices, queues, jobs, and APIs using PHP and Laravel.",
      "Released RBC QuickPay, which processed more than $50 million in transactions in its first four months.",
      "Optimized SQL and AML reporting workflows, and integrated third-party web services.",
      "Implemented JWT/OAuth authentication and strengthened testing and troubleshooting practices.",
    ],
  },
  {
    title: "PHP Full Stack Developer | Software Engineer",
    cardImage: "images/exp/nullpointers.png",
    place: "Null Pointers · Kathmandu",
    time: "February 2018 – August 2022",
    responsibilities: [
      "Built responsive frontend modules with Vue.js, JavaScript, and CSS, collaborating across the stack.",
      "Optimized PHP, Node.js, SQLite, and MySQL data operations, reducing retrieval time by 20%.",
      "Implemented live updates with Socket.IO and Pusher.",
      "Worked in Agile teams and supported release documentation, version control, and integration testing.",
    ],
  },
];

const showCards2 = () => {
  if (!experiencecards) {
    return;
  }

  let output = "";
  exp.forEach(
    ({ title, cardImage, brand, place, time, responsibilities }) => {
      const companyName = place.split(" · ")[0];
      const companyBrand = cardImage
        ? `<img src="${cardImage}" class="featured-image" alt="${companyName} logo"/>`
        : `<div class="experience-brand ${brand ? "visiontale-wordmark" : ""}" aria-label="${companyName}">${brand || companyName}</div>`;

      output += `
    <div class="col gaap" data-aos="fade-up" data-aos-easing="linear" data-aos-delay="100" data-aos-duration="400"> 
      <div class="card card1">
        ${companyBrand}
        <article class="card-body">
          <header>
            <div class="title">
              <h3>${title}</h3>
            </div>
            <p class="meta">
              <span class="pre-heading">${place}</span><br>
              <span class="author">${time}</span>
            </p>
            <ul>${responsibilities.map((responsibility) => `<li>${responsibility}</li>`).join("")}</ul>
          </header>
        </article>
      </div>
    </div>
      `;
    }
  );
  experiencecards.innerHTML = output;
};
document.addEventListener("DOMContentLoaded", showCards2);
