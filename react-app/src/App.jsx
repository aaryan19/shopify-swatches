import { LocationProvider } from './context/LocationContext';
import LocationPopup from './components/LocationPopup';
import LocationBanner from './components/LocationBanner';

function App() {
  return (
      <>
        <LocationBanner />
        <LocationPopup />
      </>
  );
}

export default App;
