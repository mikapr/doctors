class Doctors extends React.Component {
    render() {
        return (
            React.createElement("div", null, 
            "Start ", this.props.name
    )
    );
    }
}

React.render(
    React.createElement(Doctors, {name: "App"}),
    document.getElementById('main-body')
);