// import { useState } from "react";
// function App() {
//   const[traveling,settraveling]=useState(false);

//   return(
//     <>
//     <h2>{traveling ? "on mountain":"treck mountain"}</h2>
//     <button onClick={()=>settraveling(!traveling)}>{traveling ? "tracking" : "not tracking"}</button>
//     </>
//   )
// }
// export default App;


function Student(props) {
return (
  <>
  <h1>Name : {props.name}</h1>
   <p>Age : {props.age}</p>
   <p>City : {props.city}</p>
  </>
);
}

function App() {
   return(
    <>
  <Student name="ajay" age={23} city="pune"/>
    </>
   );
}
export default App;