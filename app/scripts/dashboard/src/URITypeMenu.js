import Select from "./Select";
import { uris, urls } from "./CouponURLs";
import { useDispatch, useSelector } from "react-redux";
import actions from "./actions/Actions";

const URITypeMenu = () => {
    const type = useSelector(state => state.uri.type)
    const dispatch = useDispatch();
    
    return <Select getValue={() => type} 
                    options={uris} 
                    onChange={type => dispatch(actions.updateURIType(type))}
                    button={(Button) => 
                        <Button className="flex flex-col items-start justify-center hover:bg-gray-150 rounded-5 p-2 -m-2">
                            <p className="text-smaller-2 text-gray-400">{uris.find(({id}) => id === type).name}</p>
                            <span className="border-none bg-transparent text-2x text-gray-550">{urls.homepageNoProtocol}</span>
                        </Button>
            }/>;
}

export default URITypeMenu;