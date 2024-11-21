<div>
    <div>
        <strong>Company Name:</strong> {{ $application->coName }}
    </div>
    <div>
        <strong>Company Address:</strong> {{ $application->coAddress }}
    </div>
    <div>
        <strong>Registration Number:</strong> {{ $application->regNB }}
    </div>
    <div>
        <strong>Activity:</strong> {{ $application->activity }}
    </div>
    <div>
        <strong>Number of Employees:</strong> {{ $application->empNB }}
    </div>
    <div>
        <strong>Contact Person:</strong> {{ $application->cPerson }}
    </div>
    <div>
        <strong>Contact Email:</strong> {{ $application->cEmail }}
    </div>
    <div>
        <strong>Contact Phone:</strong> {{ $application->cPhone }}
    </div>
    <div>
        <strong>Remarks:</strong> {{ $application->remark }}
    </div>
    <div>
        <strong>Quality Certification:</strong> {{ $application->qualCertif}}
    </div>
    <div>
        <strong>Product Registration:</strong> <a href="{{ url($application->prodReg) }}" download>
            <div style="display:flex;align-items:flex-end;gap:5px;color:blue;">Download file</div>
        </a>
    </div>
    <div>
        <strong>Factory Certification:</strong> <a href="{{ url($application->facCertif) }}" download>
            <div style="display:flex;align-items:flex-end;gap:5px;color:blue;">Download file</div>
        </a>
    </div>
</div>
