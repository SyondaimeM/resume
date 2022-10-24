AOS.init();

//  Work experience cards

const experiencecards = document.querySelector(".experience-cards");
const exp = [
  {
    title: "Software Engineer",
    cardImage: "images/exp/nullpointers.png",
    place: "Null Pointers",
    time: "(Feb, 2019 - Sept, 2022)",
    desp: "<li>Designed and developed system database using MySQL.</li> <li>Solve complex performance problems and architectural challenges</li><li>Writing back-end code and building efficient, testable, and reusable PHP modules.</li>",
  },
  {
    title: "Software Engineer",
    cardImage: "images/exp/tech.png",
    place: "Tech Community Nepal | Stylus",
    time: "(Apr, 2021 - Sept, 2022)",
    desp: "<li>Participated actively in Agile Scrum planning meetings and converted tasks into simple stories.</li><li>Designed system architecture through all phases of software development life cycle.</li><li>Performed Unit testing, debugging, troubleshooting, and upgrading of existing software.</li>",
  },
  {
    title: "Computer Teacher",
    cardImage: "images/exp/usna.jpg",
    place: "Ujjwal Shishu Niketan Academy",
    time: "(Mar, 2018 - Feb, 2021)",
    desp: "<li>Taught about Computer and Computer Programming</li><li>Helped to tackle with problems by making large problem to simple tasks.</li> <li>Monitored students to create short and reusable codes.",
  },
];

const showCards2 = () => {
  let output = "";
  exp.forEach(
    ({ title, cardImage, place, time, desp }) =>
      (output += `        
    <div class="col gaap" data-aos="fade-up" data-aos-easing="linear" data-aos-delay="100" data-aos-duration="400"> 
      <div class="card card1">
        <img src="${cardImage}" class="featured-image"/>
        <article class="card-body">
          <header>
            <div class="title">
              <h3>${title}</h3>
            </div>
            <p class="meta">
              <span class="pre-heading">${place}</span><br>
              <span class="author">${time}</span>
            </p>
            <ol>
              ${desp}
            </ol>
          </header>
        </article>
      </div>
    </div>
      `)
  );
  experiencecards.innerHTML = output;
};
document.addEventListener("DOMContentLoaded", showCards2);
